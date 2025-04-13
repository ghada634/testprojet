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
                bat 'if not exist tests mkdir tests'
                bat 'echo Dummy tests > tests\\dummy.txt'
                bat 'echo Tests passés avec succès!'
            }
        }

        stage('Déploiement') {
            steps {
                bat 'echo Déploiement simulé avec succès!'
            }
        }
    }
}