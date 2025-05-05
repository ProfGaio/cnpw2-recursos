# Script PowerShell para criar bucket S3 com LocalStack


$Endpoint = "http://localhost:4566"
$Bucket = Read-Host -Prompt "Insira o nome do bucket:"


# 1. Criar um bucket
Write-Host "Criando o bucket $Bucket..."
aws s3 mb "s3://$Bucket" --endpoint-url $Endpoint --profile "localstack"

# 2. Lista o bucket criado
aws s3 ls "s3://$Bucket" --endpoint-url $Endpoint --profile "localstack"