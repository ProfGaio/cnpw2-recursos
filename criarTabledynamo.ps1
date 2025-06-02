# Configuração da AWS CLI para LocalStack
aws configure set aws_access_key_id test
aws configure set aws_secret_access_key test
aws configure set region us-east-1

# Alias para facilitar
$localEndpoint = "http://localhost:4566"

# Criar a tabela
aws dynamodb create-table `
    --table-name Alunos `
    --attribute-definitions AttributeName=RA,AttributeType=S `
    --key-schema AttributeName=RA,KeyType=HASH `
    --provisioned-throughput ReadCapacityUnits=5,WriteCapacityUnits=5 `
    --endpoint-url $localEndpoint

Write-Output "✅ Tabela criada com sucesso."

