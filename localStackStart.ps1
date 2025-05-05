# Defina seu perfil AWS
$awsProfileName = "localstack"
$awsAccessKey = "test"
$awsSecretKey = "test"
$awsRegion = "us-east-1"

# Caminhos para os arquivos de configuração
$awsDir = "$HOME\.aws"
$credentialsFile = "$awsDir\credentials"
$configFile = "$awsDir\config"

# Criação da pasta .aws se não existir
if (-Not (Test-Path $awsDir)) {
    New-Item -ItemType Directory -Path $awsDir | Out-Null
}

# Criar ou sobrescrever o arquivo credentials
Set-Content -Path $credentialsFile -Value @"
[$awsProfileName]
aws_access_key_id = $awsAccessKey
aws_secret_access_key = $awsSecretKey
"@

# Criar ou sobrescrever o arquivo config
Set-Content -Path $configFile -Value @"
[profile $awsProfileName]
region = $awsRegion
output = json
"@

Write-Host "Arquivos de perfil AWS criados em $awsDir."

# Iniciar o Docker se necessário
if ((Get-Service -Name "com.docker.service" -ErrorAction SilentlyContinue).Status -ne 'Running') {
    Write-Host "Iniciando o serviço Docker..."
    Start-Service -Name "com.docker.service"
    Start-Sleep -Seconds 10
} else {
    Write-Host "Docker já está em execução."
}

# Verificar se o LocalStack container está rodando
$localstackRunning = docker ps --filter "name=localstack_main" --format "{{.Names}}"

if (-not $localstackRunning) {
    Write-Host "Iniciando o container LocalStack..."
    docker run --rm -d -p 4566:4566 -p 4571:4571 --name localstack_main localstack/localstack
} else {
    Write-Host "LocalStack já está rodando como container: $localstackRunning"
}
