Put your Aiven CA certificate here as: aiven-ca.pem

Where to get it:
1. Open your service in the Aiven Console.
2. Go to the service's "Overview" tab.
3. Under "Connection information", download the CA certificate
   (sometimes labeled "CA Certificate" or found under the padlock/TLS
   section).
4. Save it into this folder as exactly: certs/aiven-ca.pem

This file gets committed to your repo and deployed with the rest of the
project — it's a public certificate authority cert, not a secret, so
that's safe. It's only used locally by includes/initialize.php to verify
the TLS connection to Aiven when DB_SSL=true.
