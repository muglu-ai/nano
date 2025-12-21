#!/bin/bash

# TiDB Cloud Connection Diagnostic Script

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔍 TiDB Cloud Connection Diagnostics"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Get current public IP
echo "📡 Your Current Public IP Address:"
CURRENT_IP=$(curl -s ifconfig.me 2>/dev/null || curl -s ipinfo.io/ip 2>/dev/null || echo "Unable to determine")
echo "   $CURRENT_IP"
echo ""

# Test DNS resolution
echo "🌐 DNS Resolution Test:"
HOST="gateway01.ap-southeast-1.prod.aws.tidbcloud.com"
echo "   Resolving: $HOST"
nslookup "$HOST" 2>/dev/null | grep -A 2 "Name:" || echo "   ⚠️  DNS resolution failed"
echo ""

# Test port connectivity
echo "🔌 Port Connectivity Test (Port 4000):"
echo "   Testing connection to $HOST:4000..."

# Try with timeout
if timeout 5 bash -c "echo > /dev/tcp/$HOST/4000" 2>/dev/null; then
    echo "   ✅ Port 4000 is reachable"
else
    echo "   ❌ Port 4000 is NOT reachable (Connection refused)"
    echo ""
    echo "   Possible causes:"
    echo "   1. IP address $CURRENT_IP is not whitelisted in TiDB Cloud"
    echo "   2. TiDB Cloud cluster is paused or stopped"
    echo "   3. Firewall is blocking the connection"
    echo "   4. Network routing issue"
    echo ""
fi

# Test with nc (netcat) if available
if command -v nc &> /dev/null; then
    echo ""
    echo "🔌 Netcat Test:"
    nc -zv -w 5 "$HOST" 4000 2>&1
fi

# Test with telnet if available
if command -v telnet &> /dev/null; then
    echo ""
    echo "🔌 Telnet Test (will timeout after 5 seconds):"
    timeout 5 telnet "$HOST" 4000 2>&1 | head -3 || echo "   Connection failed"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "💡 Next Steps:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "1. Verify your IP ($CURRENT_IP) is whitelisted in TiDB Cloud console:"
echo "   - Go to TiDB Cloud Console"
echo "   - Select your cluster"
echo "   - Go to Security → IP Access List"
echo "   - Ensure $CURRENT_IP is listed"
echo ""
echo "2. Check cluster status:"
echo "   - Ensure cluster is running (not paused)"
echo "   - Verify cluster is in 'Available' state"
echo ""
echo "3. Verify connection details:"
echo "   - Host: gateway01.ap-southeast-1.prod.aws.tidbcloud.com"
echo "   - Port: 4000"
echo "   - Database: test"
echo "   - Username: 3L3V57kDiAbfpMs.root"
echo ""
echo "4. If using VPN/Proxy:"
echo "   - Disable VPN and try again"
echo "   - Or whitelist the VPN's exit IP"
echo ""
echo "5. Check TiDB Cloud documentation:"
echo "   - Connection may require specific client configuration"
echo "   - Some regions may have different connection requirements"
echo ""
