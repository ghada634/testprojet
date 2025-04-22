pipeline {
    agent any

    stages {
        // Cloner le code depuis le dépôt Git
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        // Exécuter les tests
        stage('Exécuter les tests') {
            steps {
                script {
                    // Exécution des tests PHPUnit
                    bat '.\\vendor\\bin\\phpunit tests'

                }
            }
        }

        // Analyser le code avec SonarQube
        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('SonarQubeServer') {
                    bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
                }
            }
        }

        // Construire l'image Docker
        stage('Construire l\'image Docker') {
            steps {
                script {
                    // Construction de l'image Docker
                    bat 'docker build -t edoc-app .'
                }
            }
        }

        // Déployer l'application
        stage('Déploiement') {
            steps {
                script {
                    // Déployer le conteneur Docker
                 
                    bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                }
            }
        }
    }
}
