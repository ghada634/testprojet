pipeline {
    agent any

    environment {
        RECIPIENTS = 'ghadaderouiche8@gmail.com'
        NESSUS_API_KEY = 'd3585e1749cc5a396b469d42e4bde871f8ea3232b5afecb69e02b6694defb17c'
        NESSUS_SECRET_KEY = '8b2a42c3c342b2382a238810eb228df4512e69b0b1ec56202badc11cc3396013'
        NESSUS_SCAN_ID = '5' // à remplacer par l'ID de ton scan Nessus
    }

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Exécuter les tests') {
            steps {
                script {
                    bat '.\\vendor\\bin\\phpunit tests'
                }
            }
        }

        stage('Analyse SonarQube') {
            steps {
                script {
                    try {
                        withSonarQubeEnv('SonarQubeServer') {
                            bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
                        }
                    } catch (Exception e) {
                        echo "Erreur lors de l'analyse SonarQube : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Construire l\'image Docker') {
            steps {
                script {
                    try {
                        bat 'docker build -t edoc-app .'
                    } catch (Exception e) {
                        echo "Erreur lors de la construction de l'image Docker : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Scan Trivy pour vulnérabilités Docker') {
            steps {
                script {
                    try {
                        bat 'trivy image edoc-app'
                    } catch (Exception e) {
                        echo "Erreur lors du scan Trivy : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Déploiement') {
            steps {
                script {
                    try {
                        bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                        bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                        bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                    } catch (Exception e) {
                        echo "Erreur lors du déploiement du conteneur Docker : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Scan Nessus') {
            steps {
                script {
                    try {
                        echo "🔐 Déclenchement du scan Nessus via l'API..."

                        bat """
                        curl -k -X POST https://localhost:8834/scans/%NESSUS_SCAN_ID%/launch ^
                        -H "X-ApiKeys: accessKey=%NESSUS_API_KEY%; secretKey=%NESSUS_SECRET_KEY%"
                        """

                        echo "🕒 Attente du scan (60s)..."
                        sleep 60

                        echo "📥 Téléchargement des résultats du scan Nessus..."
                        bat """
                        curl -k -X GET https://localhost:8834/scans/%NESSUS_SCAN_ID% ^
                        -H "X-ApiKeys: accessKey=%NESSUS_API_KEY%; secretKey=%NESSUS_SECRET_KEY%" ^
                        -o nessus_scan_result.json
                        """
                    } catch (Exception e) {
                        echo "❌ Erreur lors de l'exécution du scan Nessus : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }
    }

    post {
        success {
            mail to: "${RECIPIENTS}",
                 subject: "✅ SUCCESS - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Bonjour Ghada,\n\nLe build a réussi. Consulte les détails ici : ${env.BUILD_URL}",
                 mimeType: 'text/plain',
                 charset: 'UTF-8'
        }

        failure {
            mail to: "${RECIPIENTS}",
                 subject: "❌ ECHEC - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Bonjour Ghada 👩‍💻,\n\nLe build a échoué 💥 !\n\nVérifie les logs ici : ${env.BUILD_URL}",
                 mimeType: 'text/plain',
                 charset: 'UTF-8'
        }
    }
}
