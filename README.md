PS C:\Users\Mahran> java --version
openjdk 17.0.14 2025-01-21
OpenJDK Runtime Environment Temurin-17.0.14+7 (build 17.0.14+7)
OpenJDK 64-Bit Server VM Temurin-17.0.14+7 (build 17.0.14+7, mixed mode, sharing)
PS C:\Users\Mahran> cd C:\sonarqube\sonarqube-25.4.0.105899\bin\windows-x86-64
PS C:\sonarqube\sonarqube-25.4.0.105899\bin\windows-x86-64> .\StartSonar.bat
Starting SonarQube...
2025.04.18 11:40:55 INFO app[][o.s.a.AppFileSystem] Cleaning or creating temp directory C:\sonarqube\sonarqube-25.4.0.105899\temp
2025.04.18 11:40:55 INFO app[][o.s.a.es.EsSettings] Elasticsearch listening on [HTTP: 127.0.0.1:9001, TCP: 127.0.0.1:{}]
2025.04.18 11:40:55 INFO app[][o.s.a.ProcessLauncherImpl] Launch process[ELASTICSEARCH] from [C:\sonarqube\sonarqube-25.4.0.105899\elasticsearch]: C:\Users\Mahran\AppData\Local\Programs\Eclipse Adoptium\jdk-17.0.14.7-hotspot\bin\java -Xms4m -Xmx64m -XX:+UseSerialGC -Dcli.name=server -Dcli.script=./bin/elasticsearch -Dcli.libs=lib/tools/server-cli -Des.path.home=C:\sonarqube\sonarqube-25.4.0.105899\elasticsearch -Des.path.conf=C:\sonarqube\sonarqube-25.4.0.105899\temp\conf\es -Des.distribution.type=tar -cp C:\sonarqube\sonarqube-25.4.0.105899\elasticsearch\lib\*;C:\sonarqube\sonarqube-25.4.0.105899\elasticsearch\lib\cli-launcher\* org.elasticsearch.launcher.CliToolLauncher
2025.04.18 11:40:55 INFO app[][o.s.a.SchedulerImpl] Waiting for Elasticsearch to be up and running
Standard Commons Logging discovery in action with spring-jcl: please remove commons-logging.jar from classpath in order to avoid potential conflicts
