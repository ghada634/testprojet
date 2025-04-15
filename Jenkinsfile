pipeline {
    agent any

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Installer Composer') {
            steps {
                bat 'php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'
                bat 'php composer-setup.php'
                bat 'php composer.phar install'
            }
        }

        stage('Tests') {
            steps {
                bat 'php vendor\\bin\\phpunit tests'
            }
        }

        // 👉 STAGE AJOUTÉ POUR SONARQUBE
        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv('SonarQubeServer') {
                    bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
                }
            }
        }

        stage('Déploiement') {
            steps {
                bat 'echo Déploiement simulé avec succès!'
            }
        }
    }
}
