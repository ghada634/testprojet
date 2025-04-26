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

        stage('Pusher l\'image Docker vers Docker Hub') {
            steps {
                script {
                    try {
                        bat "docker login -u %DOCKER_USERNAME% -p %DOCKER_PASSWORD%"
                        bat "docker tag edoc-app %DOCKER_USERNAME%/edoc-app:latest"
                        bat "docker push %DOCKER_USERNAME%/edoc-app:latest"
                    } catch (Exception e) {
                        echo "Erreur lors du push de l'image Docker : ${e.getMessage()}"
                        currentBuild.result = 'FAILURE'
                        throw e
                    }
                }
            }
        }

        // 🚀 Déploiement AWS doit être à l'intérieur de "stages"
        stage('Déploiement sur AWS EC2') {
            steps {
                script {
                    sshagent(credentials: ['ghada-key']) {
                        sh '''
                            echo "Connecting to EC2 instance..."
                            ssh -o StrictHostKeyChecking=no ubuntu@54.211.241.114 << 'EOF'
                            echo "Stopping old container if any..."
                            docker stop edoc-container || echo "No container to stop"
                            docker rm edoc-container || echo "No container to remove"
                            
                            echo "Building and running the new container..."
                            cd /home/ubuntu/your-project-folder || exit
                            docker build -t edoc-app .
                            docker run -d -p 8080:80 --name edoc-container edoc-app
                            EOF
                        '''
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
