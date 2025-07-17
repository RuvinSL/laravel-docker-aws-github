https://www.duckdns.org/domains


## 🐥 DuckDNS Setup for Public Access via Traefik

### 1. Register a DuckDNS domain

- Go to: `https://www.duckdns.org`
- Log in via GitHub, Google, or Twitter
- Choose a subdomain:  
  e.g. `mylaravelapp`
- Click **Add Domain**
- Copy your **token** shown at the top

---

### 2. Create a DuckDNS update script

```bash
mkdir duckdns
cd duckdns

touch duck.sh
chmod +x duck.sh
```

Paste the following into `duck.sh`:

```bash
#!/bin/bash
DOMAIN="mylaravelapp"
TOKEN="your-duckdns-token"

echo url="https://www.duckdns.org/update?domains=$DOMAIN&token=$TOKEN&ip=" | curl -k -o duck.log -K -
```

Replace `mylaravelapp` and `your-duckdns-token` with your actual values.

---

### 3. Schedule IP auto-update (Linux/macOS)

Run `crontab -e` and add this line:

```bash
*/5 * * * * ~/duckdns/duck.sh > /dev/null 2>&1
```

💡 This updates your public IP every 5 minutes.

---

### 4. Run it once manually

```bash
./duck.sh
cat duck.log
```

If the output is `OK`, your domain now points to your current public IP.

---

### 5. Configure Traefik for DuckDNS

Update your Traefik labels (inside `docker-compose.yml`):

```yaml
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.app.rule=Host(`mylaravelapp.duckdns.org`)"
  - "traefik.http.routers.app.entrypoints=websecure"
  - "traefik.http.routers.app.tls=true"
  - "traefik.http.services.app.loadbalancer.server.port=80"
```

---

### 6. Optional: Generate SSL Certificate with `mkcert`

```bash
mkcert -cert-file cert.pem -key-file key.pem \
  mylaravelapp.duckdns.org \
  "*.duckdns.org"
```

Then mount the cert into your Traefik container and reference it in `dynamic.yml`.

---

Let me know if you want to automate certificate renewal, wire this into a `.env` file, or switch over to Let's Encrypt. I’ve got your infrastructure back. 🛠️💡