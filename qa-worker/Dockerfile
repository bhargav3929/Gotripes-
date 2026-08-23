# GoTrips QA Auditor — runs on Railway.
# Node + git + Claude Code CLI + Playwright Chromium. No PHP, no database:
# the auditor only reads code and browses the live site in a real browser.
FROM node:20-bookworm

RUN apt-get update \
  && apt-get install -y --no-install-recommends git jq curl ca-certificates \
  && rm -rf /var/lib/apt/lists/*

# Claude Code CLI (auth via CLAUDE_CODE_OAUTH_TOKEN env — subscription, no API key)
# + Playwright MCP server + Chromium with system deps
RUN npm install -g @anthropic-ai/claude-code @playwright/mcp@latest \
  && npx playwright install --with-deps chromium

WORKDIR /app
COPY package.json ./
RUN npm install --omit=dev

COPY server.js pipeline.sh mcp-playwright.json ./
RUN chmod +x pipeline.sh

# The container is an isolated sandbox; allows the Claude CLI to run
# non-interactively as root inside it.
ENV IS_SANDBOX=1
ENV PORT=8080
EXPOSE 8080
CMD ["node", "server.js"]
