pipeline {
    agent any

    environment {
        // Configuration spécifique pour votre machine
        PHP = '"C:\\xampp\\php\\php.exe"'  // Chemin PHP XAMPP par défaut
        COMPOSER = '"C:\Users\Mahran\test\composer.phar"'  // Utilise le Composer installé globalement
        LOCAL_REPO_DIR = 'C:\\Users\\Mahran\\test'  // Votre répertoire local
        DEPLOY_DIR = 'C:\\xampp\\htdocs\\eDoc'  // Répertoire de déploiement
    }

    stages {
        // Étape 1: Récupération du code
        stage('Checkout Code') {
            steps {
                git branch: 'master', 
                url: 'https://github.com/ghada634/testprojet.git'
                
                // Sauvegarde dans votre répertoire local
                bat """
                    if not exist "${LOCAL_REPO_DIR}" mkdir "${LOCAL_REPO_DIR}"
                    xcopy /Y /E /I . "${LOCAL_REPO_DIR}"
                """
            }
        }

        // Étape 2: Installation des dépendances
        stage('Install Dependencies') {
            steps {
                dir("${LOCAL_REPO_DIR}") {
                    bat """
                        ${COMPOSER} install --no-dev --optimize-autoloader
                        ${COMPOSER} dump-autoload --optimize
                    """
                }
            }
        }

        // Étape 3: Exécution des tests
        stage('Run Tests') {
            steps {
                dir("${LOCAL_REPO_DIR}") {
                    bat """
                        ${PHP} vendor\\bin\\phpunit --log-junit test-results.xml tests/
                    """
                }
            }
            post {
                always {
                    junit "${LOCAL_REPO_DIR}\\test-results.xml"
                }
            }
        }

        // Étape 4: Déploiement
        stage('Deploy') {
            steps {
                bat """
                    net stop Apache2.4 || echo "Apache non démarré"
                    robocopy "${LOCAL_REPO_DIR}" "${DEPLOY_DIR}" /MIR /NP /NFL /NDL /XD vendor tests /XF phpunit.xml .env
                    net start Apache2.4
                """
                echo '🚀 Application déployée avec succès!'
            }
        }
    }

    post {
        failure {
            emailext (
                subject: '❌ Échec du déploiement eDoc',
                body: "Build ${BUILD_NUMBER} a échoué. Voir: ${BUILD_URL}",
                to: 'votre-email@domaine.com'
            )
        }
        always {
            cleanWs()
        }
    }
}