#!/bin/bash

# ==========================================
# Script de Testing para API SistemaGestorDeAsientos
# ==========================================

BASE_URL="http://localhost/SistemaGestorDeAsientos/API/publico"
CUENTA_TEST="2219729" # Cambia esto por un número de cuenta real de tu BD para probar

# Colores para la salida
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Iniciando pruebas de la API con JWT...${NC}\n"

# ==========================================
# 1. Autenticar y obtener JWT (POST)
# ==========================================
echo -e "${YELLOW}Prueba 1: Autenticar alumno y obtener JWT ($CUENTA_TEST)...${NC}"
# API modificada: ahora usamos POST a /alumnos/validar
HTTP_STATUS=$(curl -s -o /tmp/resp1.txt -w "%{http_code}" -X POST $BASE_URL/alumnos/validar \
    -H "Content-Type: application/json" \
    -d '{
        "numero_cuenta": "'$CUENTA_TEST'"
    }')

if [ "$HTTP_STATUS" -eq 200 ]; then
    # Extraer el token usando grep y expresiones regulares (obtiene el valor de "token": "...")
    TOKEN=$(grep -o '"token":"[^"]*' /tmp/resp1.txt | awk -F'"' '{print $4}')
    
    if [ -n "$TOKEN" ]; then
        echo -e "${GREEN}✅ Éxito: El alumno existe y se obtuvo el Token JWT.${NC}"
        echo -e "${GREEN}Token obtenido: ${TOKEN:0:15}...${NC}"
    else
        echo -e "${RED}❌ Falla: El servidor devolvió 200 pero no se encontró el Token.${NC}"
        cat /tmp/resp1.txt
        exit 1
    fi
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS al hacer login${NC}"
    cat /tmp/resp1.txt
    echo ""
    exit 1
fi
echo "----------------------------------------"

# ==========================================
# 2. Confirmar asistencia (POST)
# ==========================================
echo -e "${YELLOW}Prueba 2: Confirmar asistencia del alumno (requiere JWT)...${NC}"
# API modificada: Se le quitó el /$CUENTA_TEST de la URL y mandamos Authorization header
HTTP_STATUS=$(curl -s -o /tmp/resp2.txt -w "%{http_code}" -X POST $BASE_URL/alumnos/asistencia \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d '{
        "asistira": 1,
        "num_invitados": 2,
        "correo": "test@correo.com"
    }')

if [ "$HTTP_STATUS" -eq 200 ]; then
    echo -e "${GREEN}✅ Éxito: Asistencia confirmada correctamente.${NC}"
elif [ "$HTTP_STATUS" -eq 409 ]; then
    echo -e "${YELLOW}⚠️ Aviso: El alumno ya había confirmado asistencia antes (HTTP 409). Test válido.${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
    cat /tmp/resp2.txt
    echo ""
fi
echo "----------------------------------------"

# ==========================================
# 3. Validar restricción de doble confirmación (POST)
# ==========================================
echo -e "${YELLOW}Prueba 3: Intentar confirmar asistencia por segunda vez...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp3.txt -w "%{http_code}" -X POST $BASE_URL/alumnos/asistencia \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d '{
        "asistira": 1,
        "num_invitados": 1,
        "correo": "otro@correo.com"
    }')

if [ "$HTTP_STATUS" -eq 409 ]; then
    echo -e "${GREEN}✅ Éxito: La API bloqueó el segundo intento correctamente (HTTP 409).${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 409)${NC}"
    cat /tmp/resp3.txt
    echo ""
fi
echo "----------------------------------------"

# ==========================================
# 4. Actualizar correo (POST)
# ==========================================
echo -e "${YELLOW}Prueba 4: Intentar actualizar correo de un alumno ya confirmado...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp4.txt -w "%{http_code}" -X POST $BASE_URL/alumnos/correo \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $TOKEN" \
    -d '{
        "correo": "nuevo@correo.com"
    }')

if [ "$HTTP_STATUS" -eq 409 ]; then
    echo -e "${GREEN}✅ Éxito: La API bloqueó la actualización de correo correctamente porque ya estabac confirmado (HTTP 409).${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 409)${NC}"
    cat /tmp/resp4.txt
    echo ""
fi
echo "----------------------------------------"

# ==========================================
# 5. Obtener estado del alumno (GET)
# ==========================================
echo -e "${YELLOW}Prueba 5: Consultar el estado actual del alumno...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp5.txt -w "%{http_code}" -X GET $BASE_URL/alumnos/estado \
    -H "Authorization: Bearer $TOKEN")

if [ "$HTTP_STATUS" -eq 200 ]; then
    echo -e "${GREEN}✅ Éxito: Estado obtenido correctamente.${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
    cat /tmp/resp5.txt
    echo ""
fi
echo "----------------------------------------"

# ==========================================
# 6. Validar restricción de privacidad (GET)
# ==========================================
echo -e "${YELLOW}Prueba 6: Intentar obtener todos los alumnos con token de alumno (debe fallar)...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp6.txt -w "%{http_code}" -X GET $BASE_URL/admin/alumnos \
    -H "Authorization: Bearer $TOKEN")

if [ "$HTTP_STATUS" -eq 403 ]; then
    echo -e "${GREEN}✅ Éxito: La API bloqueó el acceso a un alumno regular (HTTP 403). Privacidad asegurada.${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 403 Forbidden)${NC}"
    cat /tmp/resp6.txt
    echo ""
fi
echo "----------------------------------------"

# ==========================================
# 7. Autenticar Administrador (POST)
# ==========================================
ADMIN_USER="One"    # Ajustar a un usuario real para pruebas
ADMIN_PASS="123456"    # Ajustar a contraseña real
echo -e "${YELLOW}Prueba 7: Autenticar administrador y obtener JWT...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp7.txt -w "%{http_code}" -X POST $BASE_URL/admin/login \
    -H "Content-Type: application/json" \
    -d '{
        "usuario": "'$ADMIN_USER'",
        "contrasena": "'$ADMIN_PASS'"
    }')

if [ "$HTTP_STATUS" -eq 200 ]; then
    ADMIN_TOKEN=$(grep -o '"token":"[^"]*' /tmp/resp7.txt | awk -F'"' '{print $4}')
    echo -e "${GREEN}✅ Éxito: Administrador autenticado. Token obtenido.${NC}"
else
    echo -e "${YELLOW}⚠️ Aviso: Falló el login de administrador (HTTP $HTTP_STATUS). Puede deberse a que no existe el administrador '$ADMIN_USER' configurado en la base de datos local aún. Las pruebas que requieren este token serán omitidas.${NC}"
    cat /tmp/resp7.txt
    echo ""
fi
echo "----------------------------------------"

if [ -n "$ADMIN_TOKEN" ]; then
    # ==========================================
    # 8. Obtener lista de alumnos como admin (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 8: Obtener todos los alumnos como Administrador...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp8.txt -w "%{http_code}" -X GET $BASE_URL/admin/alumnos \
        -H "Authorization: Bearer $ADMIN_TOKEN")

    if [ "$HTTP_STATUS" -eq 200 ]; then
        echo -e "${GREEN}✅ Éxito: Listado de alumnos obtenido correctamente por el administrador.${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
        cat /tmp/resp8.txt
        echo ""
    fi
    echo "----------------------------------------"

    # ==========================================
    # 9. Obtener Métricas como admin (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 9: Obtener métricas como Administrador...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp9.txt -w "%{http_code}" -X GET $BASE_URL/admin/metricas \
        -H "Authorization: Bearer $ADMIN_TOKEN")

    if [ "$HTTP_STATUS" -eq 200 ]; then
        echo -e "${GREEN}✅ Éxito: Métricas obtenidas correctamente por el administrador.${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
        cat /tmp/resp9.txt
        echo ""
    fi
    echo "----------------------------------------"

    # ==========================================
    # 10. Obtener mapa de asientos como admin - Evento LI (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 10: Obtener mapa de asientos (evento li) como Administrador...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp10.txt -w "%{http_code}" -X GET $BASE_URL/asientos/mapa/li \
        -H "Authorization: Bearer $ADMIN_TOKEN")

    if [ "$HTTP_STATUS" -eq 200 ]; then
        echo -e "${GREEN}✅ Éxito: Mapa de asientos del evento li obtenido correctamente.${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
        cat /tmp/resp10.txt
        echo ""
    fi
    echo "----------------------------------------"

    # ==========================================
    # 11. Obtener mapa de asientos como admin - Evento LISI (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 11: Obtener mapa de asientos (evento lisi) como Administrador...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp11.txt -w "%{http_code}" -X GET $BASE_URL/asientos/mapa/lisi \
        -H "Authorization: Bearer $ADMIN_TOKEN")

    if [ "$HTTP_STATUS" -eq 200 ]; then
        echo -e "${GREEN}✅ Éxito: Mapa de asientos del evento lisi obtenido correctamente.${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
        cat /tmp/resp11.txt
        echo ""
    fi
    echo "----------------------------------------"

    # ==========================================
    # 12. Validar acceso denegado de alumno a evento diferente (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 12: Intentar obtener mapa de evento lisi con token de alumno (evento li)...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp12.txt -w "%{http_code}" -X GET $BASE_URL/asientos/mapa/lisi \
        -H "Authorization: Bearer $TOKEN")

    if [ "$HTTP_STATUS" -eq 403 ]; then
        echo -e "${GREEN}✅ Éxito: La API bloqueó el acceso al evento diferente (HTTP 403).${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 403 Forbidden)${NC}"
        cat /tmp/resp12.txt
        echo ""
    fi
    echo "----------------------------------------"

    # ==========================================
    # 13. Obtener mapa con evento de SU grupo como alumno (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 13: Obtener mapa de SU evento como alumno (con filtro por turno)...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp13.txt -w "%{http_code}" -X GET $BASE_URL/asientos/mapa/li \
        -H "Authorization: Bearer $TOKEN")

    if [ "$HTTP_STATUS" -eq 200 ]; then
        echo -e "${GREEN}✅ Éxito: Mapa de asientos del evento li obtenido para el alumno.${NC}"
        if grep -q '"mi_grupo"' /tmp/resp13.txt && grep -q '"asignado"' /tmp/resp13.txt; then
            echo -e "${GREEN}✅ La respuesta contiene mi_grupo y campo asignado.${NC}"
        else
            echo -e "${RED}❌ Falla: La respuesta no contiene los campos esperados.${NC}"
        fi
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
        cat /tmp/resp13.txt
        echo ""
    fi
    echo "----------------------------------------"

    # ==========================================
    # 14. Verificar que la respuesta no exponga datos sensibles (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 14: Verificar que la respuesta no exponga datos sensibles...${NC}"
    if [ "$HTTP_STATUS" -eq 200 ]; then
        if grep -q '"numCuenta"' /tmp/resp13.txt || grep -q '"nombre"' /tmp/resp13.txt || grep -q '"apellido"' /tmp/resp13.txt; then
            echo -e "${RED}❌ Falla: La respuesta expoe datos sensibles.${NC}"
            cat /tmp/resp13.txt
        else
            echo -e "${GREEN}✅ Éxito: La respuesta no expoe datos sensibles.${NC}"
        fi
    else
        echo -e "${YELLOW}⚠️ Prueba omitida por fallo anterior.${NC}"
    fi
    echo "----------------------------------------"

    # ==========================================
    # 15. Obtener mapa con evento inválido (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 13: Intentar obtener mapa con evento inválido...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp13.txt -w "%{http_code}" -X GET $BASE_URL/asientos/mapa/invalido \
        -H "Authorization: Bearer $ADMIN_TOKEN")

    if [ "$HTTP_STATUS" -eq 400 ]; then
        echo -e "${GREEN}✅ Éxito: La API rechazó el evento inválido (HTTP 400).${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 400 Bad Request)${NC}"
        cat /tmp/resp13.txt
        echo ""
    fi
    echo "----------------------------------------"
fi

# ==========================================
    # 16. Obtener mi asiento como alumno (GET)
    # ==========================================
    echo -e "${YELLOW}Prueba 16: Obtener mi asiento como alumno ($CUENTA_TEST)...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp14.txt -w "%{http_code}" -X GET $BASE_URL/asientos/misAsiento \
    -H "Authorization: Bearer $TOKEN")

if [ "$HTTP_STATUS" -eq 200 ]; then
    echo -e "${GREEN}✅ Éxito: Mi asiento obtenido correctamente.${NC}"
elif [ "$HTTP_STATUS" -eq 404 ]; then
    echo -e "${YELLOW}⚠️ Aviso: El alumno no tiene asiento asignado (HTTP 404).${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS${NC}"
    cat /tmp/resp14.txt
    echo ""
fi
echo "----------------------------------------"

# ==========================================
# 17. Validar acceso denegado de admin a mi asiento (GET)
# ==========================================
if [ -n "$ADMIN_TOKEN" ]; then
    echo -e "${YELLOW}Prueba 17: Intentar obtener mi asiento con token de admin (debe fallar)...${NC}"
    HTTP_STATUS=$(curl -s -o /tmp/resp17.txt -w "%{http_code}" -X GET $BASE_URL/asientos/misAsiento \
        -H "Authorization: Bearer $ADMIN_TOKEN")

    if [ "$HTTP_STATUS" -eq 403 ]; then
        echo -e "${GREEN}✅ Éxito: La API bloqueó el acceso de admin a mi asiento (HTTP 403).${NC}"
    else
        echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 403 Forbidden)${NC}"
        cat /tmp/resp17.txt
        echo ""
    fi
    echo "----------------------------------------"
fi

# ==========================================
# 18. Intentar obtener mi asiento sin token (GET)
# ==========================================
echo -e "${YELLOW}Prueba 18: Intentar obtener mi asiento sin token (debe fallar)...${NC}"
HTTP_STATUS=$(curl -s -o /tmp/resp18.txt -w "%{http_code}" -X GET $BASE_URL/asientos/misAsiento)

if [ "$HTTP_STATUS" -eq 401 ]; then
    echo -e "${GREEN}✅ Éxito: La API rejected la petici\u00f3n sin token (HTTP 401).${NC}"
else
    echo -e "${RED}❌ Falla: HTTP $HTTP_STATUS (Se esperaba 401 Unauthorized)${NC}"
    cat /tmp/resp18.txt
    echo ""
fi
echo "----------------------------------------"

echo -e "\n${YELLOW}Pruebas terminadas.${NC}"
