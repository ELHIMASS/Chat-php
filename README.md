# TP DevOps - Projet Final

Déploiement d'une infrastructure complète composée d'un chat PHP, d'un site WordPress et d'une stack de monitoring (Prometheus + Alertmanager), orchestrée via Coolify avec CI/CD automatisé.

---

## Architecture du projet

```
chat-php-main/
├── index.php                          # Application chat PHP
├── web.sql                            # Schéma de la base de données
├── Dockerfile                         # Image Docker PHP 8.2 + Apache
├── docker-compose.yml                 # Stack complète (chat + DB + WordPress)
├── .github/
│   └── workflows/
│       └── deploy.yml                 # Pipeline CI/CD GitHub Actions
└── monitoring/
    ├── docker-compose.monitoring.yml  # Stack monitoring
    ├── prometheus.yml                 # Configuration Prometheus
    ├── alert_rules.yml                # Règles d'alertes
    └── alertmanager.yml               # Configuration Alertmanager
```

---

## Services déployés

| Service        | URL / Port                    | Description                       |
|----------------|-------------------------------|-----------------------------------|
| Chat PHP       | `http://<IP>:8080`            | Application de chat               |
| WordPress      | `http://<IP>:8081`            | Site WordPress                    |
| Prometheus     | `http://<IP>:9090`            | Interface Prometheus              |
| Alertmanager   | `http://<IP>:9093`            | Interface Alertmanager            |
| Node Exporter  | `http://<IP>:9100/metrics`    | Métriques hôte                    |
| cAdvisor       | `http://<IP>:8088`            | Métriques des conteneurs         |

---

## Déploiement

### Prérequis

- Docker ≥ 27
- Docker Compose v2
- Accès à un serveur avec Coolify installé

### 1. Stack applicative (Chat + WordPress)

```bash
# Cloner le dépôt
git clone https://github.com/<username>/chat-php-main.git
cd chat-php-main

# Lancer la stack
docker compose up -d

# Vérifier les conteneurs
docker compose ps
```

### 2. Stack de monitoring

```bash
cd monitoring

# Lancer Prometheus + Alertmanager + exporters
docker compose -f docker-compose.monitoring.yml up -d
```

### 3. Déploiement via Coolify

L'application est déployée automatiquement via **Coolify** à chaque push sur la branche `main`.

Le pipeline CI/CD (GitHub Actions) :
1. **Build** l'image Docker et la pousse sur Docker Hub
2. **Déclenche** le webhook Coolify pour redéployer automatiquement

#### Secrets GitHub requis

| Secret                  | Description                              |
|-------------------------|------------------------------------------|
| `DOCKER_USERNAME`       | Identifiant Docker Hub                   |
| `DOCKER_PASSWORD`       | Mot de passe / token Docker Hub          |
| `COOLIFY_WEBHOOK_URL`   | URL du webhook de déploiement Coolify    |
| `COOLIFY_TOKEN`         | Token d'API Coolify (si nécessaire)      |

---

## Monitoring

### Prometheus

Accessible sur le port `9090`. Scrape :
- **node-exporter** → métriques CPU, RAM, disque de l'hôte
- **cAdvisor** → métriques des conteneurs Docker
- **Prometheus lui-même**

### Alertes configurées

| Alerte             | Condition                        | Sévérité |
|--------------------|----------------------------------|----------|
| `HighCPUUsage`     | CPU > 80% pendant 2 min          | warning  |
| `HighMemoryUsage`  | RAM > 85% pendant 2 min          | warning  |
| `DiskSpaceLow`     | Disque > 80% pendant 5 min       | warning  |
| `InstanceDown`     | Cible inaccessible > 1 min       | critical |
| `ContainerRestarting` | Conteneur redémarre souvent   | warning  |

### Alertmanager

Accessible sur le port `9093`. Envoie des alertes par **email** via SMTP.

> ⚠️ Configurer `smtp_auth_password` et les adresses email dans `monitoring/alertmanager.yml` avant le déploiement.

---

## Identifiants par défaut

| Service         | Utilisateur   | Mot de passe      |
|-----------------|---------------|-------------------|
| Chat DB         | `chatuser`    | `chatpassword`    |
| WordPress DB    | `wpuser`      | `wppassword`      |
| MariaDB root    | `root`        | `rootpassword`    |

> ⚠️ Modifier ces identifiants en production via des variables d'environnement ou secrets Docker.

---

## Liens

- **Coolify** : `https://<votre-instance-coolify>`
- **Chat PHP** : `http://<IP>:8080`
- **WordPress** : `http://<IP>:8081`
- **Prometheus** : `http://<IP>:9090`
- **Alertmanager** : `http://<IP>:9093`
