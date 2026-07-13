# Dokploy MCP Installation Guide

## Overview

The Dokploy MCP server exposes **508 tools** across **49 categories** for interacting with Dokploy — a complete deployment and infrastructure management platform.

**Features:**
- 508 API tools for complete Dokploy coverage
- 49 categories: Projects, Apps, Databases, Deployments, Backups, SSO, Docker, Notifications, etc.
- Available for: Claude Code, VS Code, Cursor, Windsurf, and other MCP clients

---

## Prerequisites

1. **Node.js >= 18.0.0** (already available on your system)
2. **A running Dokploy server** (self-hosted or cloud instance)
3. **Dokploy API Token** (generate from your Dokploy dashboard)

---

## Installation for Claude Code (VSCode Extension)

Claude Code uses VSCode's MCP configuration. Here's how to set it up:

### Step 1: Get Your Dokploy Credentials

1. Log in to your Dokploy server
2. Navigate to **Settings → API Keys** (or similar)
3. Create a new API token
4. Copy the token and your Dokploy server URL

Example:
- `DOKPLOY_URL`: `https://dokploy.example.com` (or `http://localhost:3000` for local)
- `DOKPLOY_API_KEY`: `your-api-token-here`

### Step 2: Configure MCP in Claude Code

Claude Code uses VSCode's settings. You can configure MCP in one of two ways:

#### Option A: Global Configuration (All Projects)

Edit `~/.vscode/settings.json`:

```json
{
  "modelContextProtocol.mcpServers": {
    "dokploy-mcp": {
      "command": "npx",
      "args": ["-y", "@dokploy/mcp@latest"],
      "env": {
        "DOKPLOY_URL": "https://your-dokploy-server.com",
        "DOKPLOY_API_KEY": "your-dokploy-api-token"
      }
    }
  }
}
```

#### Option B: Project-Specific Configuration

Create `.vscode/settings.json` in your project root:

```json
{
  "modelContextProtocol.mcpServers": {
    "dokploy-mcp": {
      "command": "npx",
      "args": ["-y", "@dokploy/mcp@latest"],
      "env": {
        "DOKPLOY_URL": "https://your-dokploy-server.com",
        "DOKPLOY_API_KEY": "your-dokploy-api-token"
      }
    }
  }
}
```

### Step 3: Restart Claude Code

After adding the configuration:
1. Close Claude Code completely
2. Reopen Claude Code
3. The Dokploy MCP should now be loaded

### Step 4: Verify Installation

Once Claude Code restarts, you should see "Dokploy MCP" available in the MCP tools list. You can:
- Ask Claude to list available Dokploy tools
- Deploy applications
- Manage databases
- View deployments
- Set up notifications
- Configure backups
- And much more!

---

## Alternative Runtimes

If you prefer not to use `npx`, you can use other JavaScript runtimes:

### Using Bun
```json
{
  "command": "bunx",
  "args": ["-y", "@dokploy/mcp@latest"]
}
```

### Using Deno
```json
{
  "command": "deno",
  "args": ["run", "--allow-env", "--allow-net", "npm:@dokploy/mcp@latest"]
}
```

---

## Local Development (Optional)

If you want to run Dokploy MCP locally from source:

```bash
cd /path/to/dokploy-mcp
pnpm install
pnpm run build
pnpm run start:stdio
```

Then use this config:
```json
{
  "dokploy-mcp": {
    "command": "node",
    "args": ["/path/to/dokploy-mcp/build/index.js"],
    "env": {
      "DOKPLOY_URL": "https://your-dokploy-server.com",
      "DOKPLOY_API_KEY": "your-dokploy-api-token"
    }
  }
}
```

---

## Troubleshooting

### MCP not appearing in Claude Code

1. **Check VSCode settings exist**: Ensure `~/.vscode/settings.json` has the configuration
2. **Restart Claude Code completely**: Close all windows and restart
3. **Verify Node.js**: Run `node --version` (should be >= 18)
4. **Check credentials**: Ensure `DOKPLOY_URL` and `DOKPLOY_API_KEY` are valid
5. **Network access**: Verify your machine can reach the Dokploy server

### Authentication errors

- Verify your `DOKPLOY_API_KEY` is correct
- Ensure the key hasn't expired
- Check that the key has necessary permissions
- Verify `DOKPLOY_URL` is accessible from your machine

### Connection timeout

- Check if Dokploy server is running
- Verify network connectivity to Dokploy server
- Ensure firewall isn't blocking the connection
- Try with `http://` instead of `https://` if using local Dokploy

---

## Available Dokploy Tools

Once installed, you'll have access to:

- **Projects**: Create, read, update, delete projects
- **Applications**: Deploy and manage applications
- **Databases**: Manage database instances
- **Deployments**: Monitor and control deployments
- **Backups**: Configure and restore backups
- **Notifications**: Set up alerts and notifications
- **SSO**: Configure single sign-on
- **Docker**: Manage Docker resources
- **Monitoring**: View metrics and logs
- **Templates**: Use application templates
- **And 40+ more categories!**

---

## Example Usage

Once installed, you can ask Claude:

- "Deploy a new Node.js application to Dokploy"
- "Show me all deployments in the last 7 days"
- "Create a PostgreSQL database backup"
- "List all active applications"
- "Configure email notifications for deployment failures"
- "Update environment variables for my app"

---

## Resources

- **Dokploy GitHub**: https://github.com/Dokploy/mcp
- **Dokploy Docs**: https://dokploy.com/docs
- **MCP Protocol**: https://modelcontextprotocol.io

---

## Next Steps

1. Set up a Dokploy server (if you don't have one)
2. Generate an API token
3. Add the configuration to VSCode settings
4. Restart Claude Code
5. Start deploying with AI assistance!
