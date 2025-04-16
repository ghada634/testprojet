pipeline {
    agent any

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Construire l\'image Docker') {
            steps {
                script {
                    bat 'docker build -t edoc-app .'
                }
            }
        }

        stage('Exécuter les tests') {
            steps {
                script {
                    bat 'vendor\\bin\\phpunit tests'
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

        stage('Déploiement') {
            steps {
                echo '🚀 Déploiement simulé avec succès !'
            }
        }
    }
}
