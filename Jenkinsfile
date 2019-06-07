import com.dgchealth.jenkinsLibrary.Utils
import com.dgchealth.jenkinsLibrary.dgcConstants

@Library('acadiaBuildTools@develop') _
def pullSecrets = ['reg.dgchealth.com', 'dockergroup.dgchealth.com']
def project = "mtwplatform"
def branch = env.BRANCH_NAME.replace(/\//,'-')
def label = "${project}-${branch}-${env.BUILD_NUMBER}"
def utils = new Utils(this)
def version = "$branch-${env.BUILD_NUMBER}"
def artifactVersions = [project]
def namespace = "${project}-${version}".toLowerCase()
def hostName = "https://${namespace}.qak8s.dgchealth.com"
def requiredContainers = ['kubectl', 'helm', 'python', 'docker', 'aws-cli-chrome', 'ansible-playbook', 'node', 'maven', 'sonar-scanner']
def containerTemplates = kubeUtils.getCiContainers(containerList: requiredContainers)

containerTemplates += [containerTemplate(name: 'composer', image: 'composer:latest', command: 'cat', ttyEnabled: true),
                       containerTemplate(name: 'node10', image: 'node:10-alpine', command: 'cat', ttyEnabled: true)]

podTemplate(
    name: label, cloud:'default', label: label, imagePullSecrets: pullSecrets, containers:containerTemplates,
    volumes: [
        hostPathVolume(mountPath:'/var/run/docker.sock', hostPath:'/var/run/docker.sock'),
        hostPathVolume(mountPath: '/root/.m2', hostPath: '/data/m2repo')
    ],
    idleTimeout: 30
) {
    node(label) {
        env.PROJECT = project
        def artifacts = [
            ["name":"mtwplatform", "path":"mtwplatform.tar.gz", "group":"mtwplatform", "artifact":"${branch}", "version":"0.1.0"]
        ]
        ciPipeline (
            project: env.project,
            ciImages: [
                [ name: "dgc/${env.PROJECT}-web", pathToBuildContext: "docker-src-k8s/web/" ],
                [ name: "dgc/${env.PROJECT}-php", pathToBuildContext: "docker-src-k8s/php/" ],
            ],
            umbrellaCharts: ["mtwplatform"], umbrellaChartGitUrl: "git@github.com:dgcHealth/mtwUmbrellaChart.git",
            ciArtifacts: artifacts,
            versionedArtifacts: artifactVersions,
            charts: {
                [
                    [
                        "chart": "mtwplatform-app",
                        "version": "0.1",
                        "ciOverrides": [ "images": [ 
                            "php": [ "reg": "cireg.dgchealth.com", "tag": version, "pullPolicy": "Always" ]
                        ] ],
                        "devOverrides": [ "images": [
                            "php": [ "reg": "reg.dgchealth.com", "tag": "${branch}-${utils.getShortCommitSha()}", pullPolicy: "Always" ]
                        ] ]
                    ],
                    [
                        "chart": "mtwplatform-web",
                        "version": "0.1",
                        "ciOverrides": [ "images": [ 
                            "web": [ "reg": "cireg.dgchealth.com", "tag": version, "pullPolicy": "Always" ],
                        ] ],
                        "devOverrides": [ "images": [
                            "web": [ "reg": "reg.dgchealth.com", "tag": "${branch}-${utils.getShortCommitSha()}", pullPolicy: "Always" ],
                        ] ]
                    ]
                 ]
            },
            checkout: {
                checkout scm
            },
            build: {
                stage('Build App'){
                    container('composer'){
                        sh "apk add --no-cache git"
                        sh "composer global require hirak/prestissimo"
                        sh "composer config discard-changes true"
                        sh "composer install --ignore-platform-reqs --optimize-autoloader --no-interaction --prefer-dist"
                    }
                    container('node10'){
                        dir('webroot/themes/custom/dgc') {
                            sh "npm config set unsafe-perm true"
                            sh "npm install"
                            sh "node_modules/gulp/bin/gulp.js sass"
                            sh "node_modules/gulp/bin/gulp.js javascript"
                        }
                    }

                    sh "tar cvf mtwplatform.tar.gz ."

                    sh "mkdir -p webroot/sf"
                    sh "grep 'key.pem' mtwplatform-app/templates/secret-sf-key.yaml | cut -d ' ' -f 4 | base64 -d > webroot/sf/key.pem"
                    sh "cp -R webroot confi* scripts vendor composer.json composer.lock RoboFile.php docker-src-k8s/php/"

                    sh "rm webroot/sf/key.pem"
                    sh "cp -R webroot docker-src-k8s/web/"

                }
            },
            unitTest: {
                stage('Unit Test') {
                }
            },
            sonar: {
                
            },
            deploy: {
                try {
                    def deploymentVersion = "0.1.0-${env.CHANGE_ID ? env.CHANGE_BRANCH.replaceAll('/', '-') : branch}"
                    def overrides
                    withCredentials([usernamePassword (credentialsId: dgcConstants.MTWMAILTRAPCREDS, passwordVariable: 'sespassword', usernameVariable: 'sesusername')]) {
                        overrides = "--set mtwplatform-app.sesUsername=${sesusername},mtwplatform-app.sesPassword=${sespassword}"
                    }
                    helmUtils.installChart(
                        chart: 'cicharts/mtwplatform', timeout: 1400,
                        releaseName: namespace, namespace: namespace,
                        otherArgs: overrides, version: deploymentVersion
                    )
                }
                catch (err) {
                    kubeUtils.getEnvLogs(stackName: namespace)
                    error "${err}"
                }
            },
            test: {},
            publish: {}
        )
    }
}
