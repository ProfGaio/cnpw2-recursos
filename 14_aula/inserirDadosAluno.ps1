# Configuração da AWS CLI para LocalStack
aws configure set aws_access_key_id test
aws configure set aws_secret_access_key test
aws configure set region us-east-1

# Alias para facilitar
$localEndpoint = "http://localhost:4566"


# Inserir um item
aws dynamodb put-item --table-name Alunos `
--item '{\"RA\": {\"S\": \"20250001\"}, \"Nome\": {\"S\": \"Carlos\"}, \"Curso\": {\"S\":\"Computacao em Nuvem\"}}' `
--endpoint-url $localEndpoint

Write-Output "Item inserido."


