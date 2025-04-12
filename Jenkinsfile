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
                sh '''
                php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
                php composer-setup.php
                php composer.phar install
                '''
            }
        }

        stage('Installer PHPUnit') {
            steps {
                sh 'php composer.phar require --dev phpunit/phpunit ^9'
            }
        }

        stage('Tests') {
            steps {
                sh './vendor/bin/phpunit tests'
            }
        }

        stage('Déploiement') {
            steps {
                echo 'Déployer le projet...'
                // ici tu peux mettre un script SCP, FTP ou Git push vers un serveur
            }
        }
    }
}
