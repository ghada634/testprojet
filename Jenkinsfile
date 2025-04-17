pipeline {
    agent any

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Construire et exécuter les tests avec Docker Compose') {
            steps {
                script {
                    // Construire les conteneurs et exécuter les tests
                    bat 'docker-compose up --build --abort-on-container-exit --exit-code-from app'
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

        stage('Déploiement Docker') {
            steps {
                script {
                    // Déployer l'application seule (sans la base de données si nécessaire)
                    bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                    bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                    bat 'docker build -t edoc-app .'
                    bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                }
            }
        }

        stage('Nettoyage Docker Compose') {
            steps {
                // Nettoyage des services et réseaux après test
                bat 'docker-compose down'
            }
        }
    }
}
