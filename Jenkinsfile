pipeline {
    agent any

    environment {
        SONARQUBE_ENV = 'SonarQubeServer' // Nom de l'installation SonarQube configurée dans Jenkins
        RECIPIENTS = 'ghadaderouiche8@gmail.com'    // Change cette adresse avec la tienne
    }

    stages {
        stage('Cloner le code') {
            steps {
                git 'https://github.com/ghada634/testprojet.git'
            }
        }

        stage('Installation des dépendances') {
            steps {
                bat 'composer install'
            }
        }

        stage('Exécuter les tests') {
            steps {
                bat '.\\vendor\\bin\\phpunit tests'
            }
        }

        stage('Analyse SonarQube') {
            steps {
                withSonarQubeEnv("${SONARQUBE_ENV}") {
                    bat 'sonar-scanner -Dsonar.projectKey=testprojet -Dsonar.sources=. -Dsonar.php.tests.reportPath=tests'
                }
            }
        }

        stage('Construire l\'image Docker') {
            steps {
                bat 'docker build -t edoc-app .'
            }
        }

        stage('Déploiement') {
            steps {
                bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
            }
        }
    }

    post {
        success {
            emailext(
                subject: "✅ Succès du build #${env.BUILD_NUMBER} - ${env.JOB_NAME}",
                body: """<p>Le build <b>#${env.BUILD_NUMBER}</b> s'est terminé avec succès.</p>
                         <p><a href="${env.BUILD_URL}">Voir les détails du build</a></p>""",
                mimeType: 'text/html',
                to: "${env.RECIPIENTS}"
            )
        }

        failure {
            emailext(
                subject: "❌ Échec du build #${env.BUILD_NUMBER} - ${env.JOB_NAME}",
                body: """<p>Le build <b>#${env.BUILD_NUMBER}</b> a échoué.</p>
                         <p>Consultez les logs ici : <a href="${env.BUILD_URL}">${env.BUILD_URL}</a></p>""",
                mimeType: 'text/html',
                to: "${env.RECIPIENTS}"
            )
        }

        always {
            echo "Une notification par e-mail a été envoyée selon le résultat du pipeline."
        }
    }
}
