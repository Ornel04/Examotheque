pipeline {
    agent any

    environment {
        DOCKERHUB_CREDS = 'dockerhub-creds' // ID du credential Jenkins
        IMAGE_BACKEND = 'ornel10/examotheque-backend:latest'
        IMAGE_FRONTEND = 'ornel10/examotheque-frontend:latest'
    }

    stages {

        stage('Checkout') {
            steps {
                // Récupérer le code depuis Git
                git branch: 'develop', url: 'https://github.com/Ornel04/Examotheque.git'
            }
        }

        stage('Docker Login') {
            steps {
                withCredentials([usernamePassword(credentialsId: "${DOCKERHUB_CREDS}", usernameVariable: 'DOCKER_USER', passwordVariable: 'DOCKER_PASS')]) {
                    sh 'echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin'
                }
            }
        }

        stage('Build Images') {
            steps {
                sh 'docker-compose build'
            }
        }

        stage('Push Images') {
            steps {
                sh 'docker-compose push'
            }
        }

        stage('Deploy (Optional)') {
            steps {
                  echo 'Stage déploiement optionnel désactivé'
            }
        }
    }

    post {
        always {
            sh 'docker logout'
        }
    }
}
