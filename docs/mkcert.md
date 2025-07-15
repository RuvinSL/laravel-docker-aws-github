mkcert -install

mkcert -cert-file cert.pem -key-file key.pem  192.168.101.8 "*.192.168.101.8.nip.io" "*.nip.io" "*.sslip.io"


mkcert -cert-file cert.pem -key-file key.pem  192.168.1.180 "*.192.168.1.180.nip.io" "*.nip.io" "*.sslip.io"


