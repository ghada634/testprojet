pipeline {
    agent any

    environment {
        RECIPIENTS = 'ghadaderouiche8@gmail.com' // Adresse email pour recevoir les notifications
    }

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
                    bat 'docker stop edoc-container || echo "Pas de conteneur à arrêter"'
                    bat 'docker rm edoc-container || echo "Pas de conteneur à supprimer"'
                    bat 'docker run -d -p 8082:80 --name edoc-container edoc-app'
                }
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
