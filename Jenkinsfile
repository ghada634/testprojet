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
                    bat '.\\vendor\\bin\\phpunit tests'
                }
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
                script {
                    bat 'docker build -t edoc-app .'
                }
            }
        }

        stage('Déploiement') {
            steps {
                script {
                    bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                    bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                    bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                }
            }
        }
    }

    post {
        success {
            mail to: "${RECIPIENTS}",
                 subject: "✅ SUCCESS - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Bonjour Ghada 👩‍💻,\n\nLe build a RÉUSSI 🎉 !\n\nConsulte les détails ici : ${env.BUILD_URL}",
                 mimeType: 'text/plain',
                 charset: 'UTF-8'
        }

        failure {
            mail to: "${RECIPIENTS}",
                 subject: "❌ ECHEC - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                 body: "Bonjour Ghada 👩‍💻,\n\nLe build a ÉCHOUÉ 💥 !\n\nVérifie les logs ici : ${env.BUILD_URL}",
                 mimeType: 'text/plain',
                 charset: 'UTF-8'
        }
    }
}
