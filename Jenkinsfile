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
                // Exécution réelle des tests avec PHPUnit
                bat 'php vendor\\bin\\phpunit tests'
            }
        }

        stage('Déploiement') {
            steps {
                bat 'echo Déploiement simulé avec succès!'
            }
        }
    }
}
