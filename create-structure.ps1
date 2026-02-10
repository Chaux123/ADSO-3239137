# create-structure.ps1
# Crea la estructura de carpetas para el semestre 2026-a-g1

# Banner
Write-Host @"

╔═══════════════════════════════════════════════════════════════╗
║   📁 CREADOR DE ESTRUCTURA - CORHUILA 2026-A G1              ║
║   10 semanas con 1 sesión y actividad opcional               ║
╚═══════════════════════════════════════════════════════════════╝

"@ -ForegroundColor Cyan

$basePath = $PSScriptRoot

Write-Host "`n📚 Creando estructura en carpeta actual..." -ForegroundColor Cyan

# Crear 10 semanas
for ($week = 1; $week -le 10; $week++) {
    $weekFolder = "{0:D2}-week" -f $week
    $weekPath = Join-Path $basePath $weekFolder
    
    # Crear carpeta de la semana si no existe
    if (-not (Test-Path $weekPath)) {
        New-Item -Path $weekPath -ItemType Directory -Force | Out-Null
    }
    
    # Crear 01-session
    $session1Path = Join-Path $weekPath "01-session"
    if (-not (Test-Path $session1Path)) {
        New-Item -Path $session1Path -ItemType Directory -Force | Out-Null
        New-Item -Path (Join-Path $session1Path ".gitkeep") -ItemType File -Force | Out-Null
    }
    
    # Crear 02-optional-activity
    $optionalPath = Join-Path $weekPath "02-optional-activity"
    if (-not (Test-Path $optionalPath)) {
        New-Item -Path $optionalPath -ItemType Directory -Force | Out-Null
        New-Item -Path (Join-Path $optionalPath ".gitkeep") -ItemType File -Force | Out-Null
    }
    
    Write-Host "  ✓ Semana $week creada" -ForegroundColor Green
}

Write-Host @"

╔═══════════════════════════════════════════════════════════════╗
║                    🎉 Proceso completado                     ║
║   Estructura de 10 semanas creada exitosamente              ║
╚═══════════════════════════════════════════════════════════════╝

"@ -ForegroundColor Cyan
