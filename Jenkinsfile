pipeline {
    agent any

    environment {
        RECIPIENTS = 'ghadaderouiche8@gmail.com'
        DOCKER_USERNAME = 'ghada522'
        DOCKER_PASSWORD = 'Ghoughou*2001'
    }

    stages {
        stage('Cloner le code') {
            steps {
                git url: 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Exécuter les tests') {
            steps {
                bat '.\\vendor\\bin\\phpunit tests'
            }
        }

        stage('Analyse SonarQube') {
            steps {
                script {
                    withSonarQubeEnv('SonarQubeServer') {
                        bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
                    }
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

        stage('Pusher l\'image Docker vers Docker Hub') {
            steps {
                script {
                    bat "docker login -u ${DOCKER_USERNAME} -p ${DOCKER_PASSWORD}"
                    bat "docker tag edoc-app ${DOCKER_USERNAME}/edoc-app:latest"
                    bat "docker push ${DOCKER_USERNAME}/edoc-app:latest"
                }
            }
        }

        stage('Déployer sur AWS EC2') {
            steps {
                script {
                    withCredentials([sshUserPrivateKey(credentialsId: 'ghada-key', keyFileVariable: 'SSH_KEY', usernameVariable: 'SSH_USER')]) {
                        powershell """
                            \$env:Path += ';C:\\\\Windows\\\\System32\\\\OpenSSH'

                            # Modifier les permissions pour que seul Jenkins puisse lire la clé
                            icacls "\$env:SSH_KEY" /inheritance:r
                            icacls "\$env:SSH_KEY" /grant:r "%USERNAME%:R"

                            ssh -o StrictHostKeyChecking=no -i \$env:SSH_KEY \$env:SSH_USER@54.243.15.15 `
                                "docker pull ${DOCKER_USERNAME}/edoc-app:latest && docker stop app || true && docker rm app || true && docker run -d --name app -p 8080:8080 ${DOCKER_USERNAME}/edoc-app:latest"
                        """
                    }
                }
            }
        }
    }

    post {
        success {
            mail(
                to: "${RECIPIENTS}",
                subject: "✅ SUCCESS - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                body: "Bonjour Ghada,\n\nLe build a réussi. Consulte les détails ici : ${env.BUILD_URL}",
                mimeType: 'text/plain',
                charset: 'UTF-8'
            )
        }
        failure {
            mail(
                to: "${RECIPIENTS}",
                subject: "❌ ECHEC - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                body: "Bonjour Ghada 👩‍💻,\n\nLe build a échoué 💥 !\n\nVérifie les logs ici : ${env.BUILD_URL}",
                mimeType: 'text/plain',
                charset: 'UTF-8'
            )
        }
    }
}
