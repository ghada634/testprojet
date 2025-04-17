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

        stage('Exécuter les tests dans le conteneur Docker') {
            steps {
                script {
                    // Exécuter PHPUnit à l'intérieur du conteneur Docker
                    bat 'docker run --rm edoc-app vendor/bin/phpunit tests'
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
                    // Arrêter et supprimer l'ancien conteneur, puis démarrer le nouveau
                    bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                    bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                    bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                }
            }
        }
    }
}
