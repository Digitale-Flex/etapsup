#!/bin/bash
# 🔍 Script de vérification déploiement dev.etapsup.org
# Date: 10 décembre 2025
# Usage: bash verify-deployment.sh

set -e

echo "════════════════════════════════════════════════════════════"
echo "  🔍 VÉRIFICATION DÉPLOIEMENT dev.etapsup.org"
echo "════════════════════════════════════════════════════════════"
echo ""

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

ERRORS=0

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}1️⃣  Version du code${NC}"
# ─────────────────────────────────────────────────────────────────
COMMIT=$(git log -1 --format="%h - %s")
echo "   Commit actuel: $COMMIT"
if [[ $COMMIT == *"08e03f9"* ]] || [[ $COMMIT == *"downgrade openspout"* ]]; then
    echo -e "   ${GREEN}✅ Code à jour${NC}"
else
    echo -e "   ${RED}⚠️  Commit différent du local${NC}"
fi
echo ""

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}2️⃣  Version PHP${NC}"
# ─────────────────────────────────────────────────────────────────
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2)
echo "   Version: $PHP_VERSION"
if [[ $PHP_VERSION == 8.2.* ]] || [[ $PHP_VERSION == 8.3.* ]]; then
    echo -e "   ${GREEN}✅ PHP compatible${NC}"
else
    echo -e "   ${RED}❌ PHP version incompatible${NC}"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}3️⃣  Version openspout${NC}"
# ─────────────────────────────────────────────────────────────────
OPENSPOUT=$(composer show openspout/openspout 2>/dev/null | grep "versions" | awk '{print $3}')
echo "   Version: $OPENSPOUT"
if [[ $OPENSPOUT == *"4.24"* ]]; then
    echo -e "   ${GREEN}✅ openspout 4.24 (compatible PHP 8.2)${NC}"
else
    echo -e "   ${YELLOW}⚠️  Version différente de 4.24${NC}"
fi
echo ""

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}4️⃣  État des migrations${NC}"
# ─────────────────────────────────────────────────────────────────
PENDING=$(php artisan migrate:status | grep -c "Pending" || echo "0")
echo "   Migrations en attente: $PENDING"
if [ "$PENDING" -eq "0" ]; then
    echo -e "   ${GREEN}✅ Toutes les migrations exécutées${NC}"
else
    echo -e "   ${RED}❌ $PENDING migration(s) en attente${NC}"
    php artisan migrate:status | grep "Pending"
    ERRORS=$((ERRORS + 1))
fi
echo ""

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}5️⃣  Structure base de données${NC}"
# ─────────────────────────────────────────────────────────────────
HAS_COUNTRY_ID=$(php artisan tinker --execute="echo Schema::hasColumn('cities', 'country_id') ? 'yes' : 'no';" 2>/dev/null)
HAS_REGION_ID=$(php artisan tinker --execute="echo Schema::hasColumn('cities', 'region_id') ? 'yes' : 'no';" 2>/dev/null)

if [[ $HAS_COUNTRY_ID == *"yes"* ]]; then
    echo -e "   ${GREEN}✅ Colonne cities.country_id existe${NC}"
else
    echo -e "   ${RED}❌ Colonne cities.country_id manquante${NC}"
    ERRORS=$((ERRORS + 1))
fi

if [[ $HAS_REGION_ID == *"no"* ]]; then
    echo -e "   ${GREEN}✅ Colonne cities.region_id supprimée${NC}"
else
    echo -e "   ${YELLOW}⚠️  Colonne cities.region_id encore présente${NC}"
fi
echo ""

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}6️⃣  Test des endpoints HTTP${NC}"
# ─────────────────────────────────────────────────────────────────
URLS=(
    "https://dev.etapsup.org/"
    "https://dev.etapsup.org/establishments"
    "https://dev.etapsup.org/accueil"
    "https://dev.etapsup.org/admin"
)

for URL in "${URLS[@]}"; do
    STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$URL" 2>/dev/null || echo "000")
    if [ "$STATUS" = "200" ]; then
        echo -e "   ${GREEN}✅${NC} $URL → $STATUS"
    elif [ "$STATUS" = "302" ] || [ "$STATUS" = "301" ]; then
        echo -e "   ${YELLOW}↪️${NC} $URL → $STATUS (redirection)"
    else
        echo -e "   ${RED}❌${NC} $URL → $STATUS"
        ERRORS=$((ERRORS + 1))
    fi
done
echo ""

# ─────────────────────────────────────────────────────────────────
echo -e "${BLUE}7️⃣  Logs Laravel récents${NC}"
# ─────────────────────────────────────────────────────────────────
if [ -f "storage/logs/laravel.log" ]; then
    ERROR_COUNT=$(tail -100 storage/logs/laravel.log | grep -c "ERROR" || echo "0")
    if [ "$ERROR_COUNT" -eq "0" ]; then
        echo -e "   ${GREEN}✅ Aucune erreur récente${NC}"
    else
        echo -e "   ${YELLOW}⚠️  $ERROR_COUNT erreur(s) dans les 100 dernières lignes${NC}"
        echo "   Dernières erreurs:"
        tail -100 storage/logs/laravel.log | grep "ERROR" | tail -3 | sed 's/^/     /'
    fi
else
    echo -e "   ${YELLOW}⚠️  Fichier de logs non trouvé${NC}"
fi
echo ""

# ─────────────────────────────────────────────────────────────────
echo "════════════════════════════════════════════════════════════"
if [ "$ERRORS" -eq "0" ]; then
    echo -e "${GREEN}  ✅ DÉPLOIEMENT COMPLET ET FONCTIONNEL !${NC}"
    echo "════════════════════════════════════════════════════════════"
    exit 0
else
    echo -e "${RED}  ❌ $ERRORS PROBLÈME(S) DÉTECTÉ(S)${NC}"
    echo "════════════════════════════════════════════════════════════"
    echo ""
    echo "Consultez les guides:"
    echo "  📄 FIX_MIGRATION_PERSONAL_ACCESS_TOKENS.md"
    echo "  📄 VERIF_DEPLOIEMENT_DEV.md"
    exit 1
fi
