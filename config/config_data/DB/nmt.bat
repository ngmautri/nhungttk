php doctrine-module orm:clear-cache:metadata
php doctrine-module orm:clear-cache:query
php doctrine-module orm:clear-cache:result
php doctrine-module orm:convert-mapping --namespace="Application\Entity\\" --force  --from-database annotation ./module/Application/src/
php doctrine-module orm:generate-entities ./module/Application/src/ --generate-annotations=true
