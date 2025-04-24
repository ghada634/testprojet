pipeline {
    agent any

    environment {
        RECIPIENTS = 'ghadaderouiche8@gmail.com'
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
                    try {
                        bat '.\\vendor\\bin\\phpunit tests'
                    } catch (Exception e) {
                        echo "Erreur lors de l'exécution des tests : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('SonarQubeServer') {
                    try {
                        bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
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
