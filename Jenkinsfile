pipeline {
    agent any

    environment {
        RECIPIENTS = 'ghadaderouiche8@gmail.com'
        NESSUS_HOST = 'https://localhost:8834'  // ← Adresse de ton Nessus
        NESSUS_USERNAME = 'admin'               // ← Identifiant Nessus
        NESSUS_PASSWORD = 'admin'               // ← Mot de passe Nessus
        SCAN_NAME = 'Scan-Ghada'
        TARGET_IP = '127.0.0.1'                 // ← IP à scanner
        POLICY_ID = '1'                         // ← ID de la politique Nessus
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
                    def token = bat(script: """
                        curl -k -X POST "${NESSUS_HOST}/session" -H "Content-Type: application/json" -d ^
                        "{\\"username\\": \\"${NESSUS_USERNAME}\\", \\"password\\": \\"${NESSUS_PASSWORD}\\"}" ^
                        --silent | jq -r ".token"
                    """, returnStdout: true).trim()

                    echo "Création du scan"
                    def scanId = bat(script: """
                        curl -k -X POST "${NESSUS_HOST}/scans" ^
                        -H "X-Cookie: token=${token}" -H "Content-Type: application/json" ^
                        -d "{\\"uuid\\": \\"${POLICY_ID}\\", \\"settings\\": {\\"name\\": \\"${SCAN_NAME}\\", \\"policy_id\\": ${POLICY_ID}, \\"text_targets\\": \\"${TARGET_IP}\\"}}" ^
                        --silent | jq -r ".scan.id"
                    """, returnStdout: true).trim()

                    echo "Démarrage du scan Nessus"
                    bat """
                        curl -k -X POST "${NESSUS_HOST}/scans/${scanId}/launch" ^
                        -H "X-Cookie: token=${token}"
                    """
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
