pipeline {
    agent any

    environment {
        RECIPIENTS = 'ghadaderouiche8@gmail.com'
        NESSUS_HOST = 'https://localhost:8834'
        NESSUS_USERNAME = 'ghada'
        NESSUS_PASSWORD = 'Ghoughou*2001'
        SCAN_NAME = 'pepline'
        TARGET_IP = 'localhost:8082'
        POLICY_ID = '5'
    }

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Exécuter les tests') {
            steps {
                bat '.\\vendor\\bin\\phpunit tests'
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('SonarQubeServer') {
                    bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
                }
            }
        }

        stage('Construire l\'image Docker') {
            steps {
                bat 'docker build -t edoc-app .'
            }
        }

        stage('Scan Trivy pour vulnérabilités Docker') {
            steps {
                bat 'trivy image edoc-app'
            }
        }

        stage('Déploiement Docker') {
            steps {
                script {
                    bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                    bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                    bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                }
            }
        }

        stage('Scan Nessus') {
            steps {
                script {
                    echo "Authentification à Nessus"

                    def token = powershell(returnStdout: true, script: """
                        \$response = Invoke-RestMethod -Method Post -Uri '${NESSUS_HOST}/session' `
                            -Headers @{ 'Content-Type' = 'application/json' } `
                            -Body (@{username='${NESSUS_USERNAME}'; password='${NESSUS_PASSWORD}'} | ConvertTo-Json) `
                            -SkipCertificateCheck
                        return \$response.token
                    """).trim()
                    echo "Token récupéré : ${token}"

                    echo "Création du scan"
                    def scanId = powershell(returnStdout: true, script: """
                        \$body = @{
                            uuid = '${POLICY_ID}'
                            settings = @{
                                name = '${SCAN_NAME}'
                                policy_id = ${POLICY_ID}
                                text_targets = '${TARGET_IP}'
                            }
                        } | ConvertTo-Json -Depth 3

                        \$response = Invoke-RestMethod -Method Post -Uri '${NESSUS_HOST}/scans' `
                            -Headers @{ 'X-Cookie' = 'token=${token}'; 'Content-Type' = 'application/json' } `
                            -Body \$body -SkipCertificateCheck

                        return \$response.scan.id
                    """).trim()
                    echo "Scan ID récupéré : ${scanId}"

                    echo "Lancement du scan"
                    powershell(script: """
                        Invoke-RestMethod -Method Post -Uri '${NESSUS_HOST}/scans/${scanId}/launch' `
                            -Headers @{ 'X-Cookie' = 'token=${token}' } -SkipCertificateCheck
                    """)
                }
            }
        }
    }

    post {
        success {
            mail to: "${RECIPIENTS}",
                 subject: "✅ SUCCESS - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Bonjour Ghada,\n\nLe pipeline a réussi avec succès ! 🎉\nConsulte les détails ici : ${env.BUILD_URL}",
                 mimeType: 'text/plain',
                 charset: 'UTF-8'
        }

        failure {
            mail to: "${RECIPIENTS}",
                 subject: "❌ ECHEC - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Bonjour Ghada 👩‍💻,\n\nLe pipeline a échoué 💥 !\n\nVérifie les logs ici : ${env.BUILD_URL}",
                 mimeType: 'text/plain',
                 charset: 'UTF-8'
        }
    }
}
