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

        stage('Deploy to AWS') {
            steps {
                script {
                    // Get the private key stored in Jenkins credentials
                    def privateKey = credentials('ghada-key')  // Corrected to access Jenkins credentials

                    // Write the private key to a temporary file
                    writeFile(file: 'id_rsa', text: privateKey)

                    // Use SSH to deploy, passing the private key via the file
                    bat """
                        ssh -i id_rsa -o StrictHostKeyChecking=no ubuntu@54.243.15.15 "mkdir -p ~/mahran"
                    """

                    // Delete the temporary private key file after use
                    bat 'del id_rsa'
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
