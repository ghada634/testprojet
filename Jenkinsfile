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
                        echo "Erreur lors de la construction de l\'image Docker : ${e.getMessage()}"
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

        stage('Pusher l\'image Docker vers Docker Hub') {
            steps {
                script {
                    try {
                        bat "docker login -u ${DOCKER_USERNAME} -p ${DOCKER_PASSWORD}"
                        bat "docker tag edoc-app ${DOCKER_USERNAME}/edoc-app:latest"
                        bat "docker push ${DOCKER_USERNAME}/edoc-app:latest"
                    } catch (Exception e) {
                        echo "Erreur lors du push de l'image Docker : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        stage('Déployer sur AWS EC2') {
            steps {
                script {
                    withCredentials([sshUserPrivateKey(credentialsId: 'ghada-key', keyFileVariable: 'SSH_KEY', usernameVariable: 'SSH_USER')]) {
                        bat """
                            set PATH=C:\\Windows\\System32\\OpenSSH\\;%PATH%
                            icacls %SSH_KEY% /inheritance:r
                            icacls %SSH_KEY% /grant:r "*S-1-1-0:R"
                            ssh -i %SSH_KEY% -o StrictHostKeyChecking=no -o IdentitiesOnly=yes -o UserKnownHostsFile=/dev/null %SSH_USER%@100.26.100.148 "docker pull ${DOCKER_USERNAME}/edoc-app:latest && docker stop app || true && docker rm app || true && docker run -d --name app -p 80:80 ${DOCKER_USERNAME}/edoc-app:latest"
                        """
                    }
                }
            }
        }
    }

    post {
        success {
            mail(
                to: RECIPIENTS,
                subject: "✅ SUCCESS - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                body: "Bonjour Ghada,\n\nLe build a réussi. Consulte les détails ici : ${env.BUILD_URL}",
                mimeType: 'text/plain',
                charset: 'UTF-8'
            )
        }

        failure {
            mail(
                to: RECIPIENTS,
                subject: "❌ ECHEC - ${env.JOB_NAME} #${env.BUILD_NUMBER}",
                body: "Bonjour Ghada 👩‍💻,\n\nLe build a échoué 💥 !\n\nVérifie les logs ici : ${env.BUILD_URL}",
                mimeType: 'text/plain',
                charset: 'UTF-8'
            )
        }
    }
}
