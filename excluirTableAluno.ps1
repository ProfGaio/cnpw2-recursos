# Configuração da AWS CLI para LocalStack
aws configure set aws_access_key_id test
aws configure set aws_secret_access_key test
aws configure set region us-east-1

# Alias para facilitar
$localEndpoint = "http://localhost:4566"

# Excluir item
aws dynamodb delete-item `
    --table-name Alunos `
    --key '{"RA": {"S": "20250001"}}' `
    --endpoint-url $localEndpoint

Write-Output "🗑️ Item excluído."
