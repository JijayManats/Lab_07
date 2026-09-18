<?php
// Direct access guard
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Stores PHP sessions in a MySQL table instead of the local filesystem.
 *
 * This is required on serverless platforms like Vercel: each request can be
 * handled by a fresh, ephemeral container, so file-based sessions (the PHP
 * default) would not be visible to the next request and users would appear
 * logged out immediately after logging in. Works fine locally too.
 */
class DbSessionHandler implements SessionHandlerInterface
{
    private mysqli $connection;
    private int $lifetime;

    public function __construct(mysqli $connection, int $lifetime = 1440)
    {
        $this->connection = $connection;
        $this->lifetime = $lifetime;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $cutoff = time() - $this->lifetime;
        $stmt = $this->connection->prepare("SELECT data FROM sessions WHERE id = ? AND last_activity > ?");
        $stmt->bind_param("si", $id, $cutoff);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? $row['data'] : '';
    }

    public function write(string $id, string $data): bool
    {
        $now = time();
        $stmt = $this->connection->prepare(
            "INSERT INTO sessions (id, data, last_activity) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE data = VALUES(data), last_activity = VALUES(last_activity)"
        );
        $stmt->bind_param("ssi", $id, $data, $now);
        return $stmt->execute();
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM sessions WHERE id = ?");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function gc(int $max_lifetime): int|false
    {
        $cutoff = time() - $max_lifetime;
        $stmt = $this->connection->prepare("DELETE FROM sessions WHERE last_activity < ?");
        $stmt->bind_param("i", $cutoff);
        $stmt->execute();
        return $this->connection->affected_rows;
    }
}
?>
