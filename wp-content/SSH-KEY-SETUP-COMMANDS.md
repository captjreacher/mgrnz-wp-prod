# Fix GitHub Actions SSH Deployment

## Step 1: Generate SSH Key Pair (Run on your local machine)

```powershell
# Generate a new SSH key pair
ssh-keygen -t ed25519 -C "github-actions-deploy" -f "$env:USERPROFILE\.ssh\mgrnz_deploy_key" -N '""'
```

This creates:
- `%USERPROFILE%\.ssh\mgrnz_deploy_key` (private key)
- `%USERPROFILE%\.ssh\mgrnz_deploy_key.pub` (public key)

## Step 2: Display the Keys

```powershell
# Show the PUBLIC key (this goes on your server)
type "$env:USERPROFILE\.ssh\mgrnz_deploy_key.pub"

# Show the PRIVATE key (this goes in GitHub Secrets)
type "$env:USERPROFILE\.ssh\mgrnz_deploy_key"
```

## Step 3: Add Public Key to Production Server

SSH into your production server and run:

```bash
# Create .ssh directory if it doesn't exist
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Add the public key to authorized_keys
echo "PASTE_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

Replace `PASTE_PUBLIC_KEY_HERE` with the output from the public key command above.

## Step 4: Add Private Key to GitHub Secrets

1. Go to: https://github.com/captireacher/mgrnz-wp/settings/secrets/actions
2. Click "New repository secret"
3. Name: `SSH_PRIVATE_KEY` (or whatever your workflow expects)
4. Value: Paste the ENTIRE private key (including `-----BEGIN OPENSSH PRIVATE KEY-----` and `-----END OPENSSH PRIVATE KEY-----`)
5. Click "Add secret"

## Step 5: Verify Your GitHub Actions Workflow

Make sure your workflow file (`.github/workflows/deploy.yml` in the mgrnz-wp repo) has:

```yaml
- name: Setup SSH
  run: |
    mkdir -p ~/.ssh
    echo "${{ secrets.SSH_PRIVATE_KEY }}" > ~/.ssh/deploy_key
    chmod 600 ~/.ssh/deploy_key
    ssh-keyscan -H YOUR_SERVER_IP >> ~/.ssh/known_hosts

- name: Deploy via rsync
  run: |
    rsync -avz --delete \
      -e "ssh -i ~/.ssh/deploy_key -o StrictHostKeyChecking=no" \
      ./ user@YOUR_SERVER_IP:/path/to/wordpress/
```

## Step 6: Test the Connection

Before running the GitHub Action, test the SSH connection locally:

```powershell
ssh -i "$env:USERPROFILE\.ssh\mgrnz_deploy_key" user@YOUR_SERVER_IP
```

If this works, the GitHub Action should work too.

## Troubleshooting

If it still fails:
- Check the username and server IP in your workflow file
- Verify the deployment path on the server
- Make sure the SSH key has no passphrase (we used `-N '""'`)
- Check server logs: `tail -f /var/log/auth.log` (on the server)
