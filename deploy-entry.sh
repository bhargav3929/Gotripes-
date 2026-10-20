#!/bin/bash
# The only thing the GitHub Actions deploy key may run on the server.
#
# ~/.ssh/authorized_keys on the server pins that key to this script:
#   command="bash ~/domains/gotrips.ai/public_html/deploy-entry.sh",no-pty,no-port-forwarding,no-agent-forwarding,no-X11-forwarding ssh-ed25519 AAAA... github-actions-deploy
# so whatever the key asks for, only "deploy" or "check" can happen.
# See docs/DEPLOYING.md.

case "${SSH_ORIGINAL_COMMAND:-deploy}" in
    deploy) exec bash "$HOME/domains/gotrips.ai/public_html/deploy.sh" github ;;
    check)  exec bash "$HOME/domains/gotrips.ai/public_html/deploy.sh" --check ;;
    *)      echo "Not allowed: only 'deploy' or 'check'." >&2; exit 64 ;;
esac
