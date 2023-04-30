> :warning: This repository is now deprecated and will receive no further updates.
> Users are advised to use the [Keptn Lifecycle Toolkit](https://lifecycle.keptn.sh) instead.

# OSTicket Service
This service creates tickets in [OSTicket](https://github.com/osTicket/osTicket) when a keptn evaluation (`sh.keptn.event.start-evaluation`) is performed. The service subscribes to the following keptn events:

* `sh.keptn.events.evaluation-done`

![osticket screenshot](/assets/osticket-screenshot.png)

# Installation

## Install & Configure OSTicket

:warning: Keptn will send the request to create the ticket. Ensure you allow incoming traffic from the Keptn machine to the OSTicket machine.

To use this service, you need a running OSTicket system. If you need to create one, use the `osTicketInstall.sh` file in the `osticket-setup-files` folder.

```
cd ~/keptn-osticket-service/osticket-setup-files
chmod +x osTicketInstall.sh && chmod +x cleanup.sh
./osTicketInstall.sh
```

After installation, you'll get an HTTP 500 error. Just refresh the page. Then run the cleanup script.

```
./cleanup.sh
```

:warning: This OSTicket installation script is NOT secure and meant only for demo purposes.

You will require an OSTicket API key with `Create Ticket` permissions.

1. Go to `http://OSTICKET-IP/scp/apikeys.php?a=add` generate an API key (use the Keptn IP).
1. Make a note of the OSTicket URL. It should be like: `http://123.111.222.333` (without trailing slash).
1. Make a note of the OSTicket API key you created.

# Install OSTicket Service into Keptn Cluster
1. Clone this repo onto the keptn machine.
1. Create a secret to hold the OSTicket URL, API Key (substituting the values for **your** values below):

```
kubectl create secret generic osticket-details -n keptn --from-literal=url='http://1.2.3.4' --from-literal=api-key='abcd1234'
```
3. Use kubectl to apply both the `osticket-service.yaml` and `osticket-distributor.yaml` files on the keptn cluster:

```
cd ~/keptn-osticket-service
kubectl apply -f osticket-service.yaml -f osticket-distributor.yaml
```

Expected output:
```
deployment.apps/osticket-service created
service/osticket-service created
deployment.apps/osticket-service-distributor created
```

# Verification of Installation
```
kubectl -n keptn get pods | grep osticket
```

Expected output:
```
osticket-service-*-*                                 1/1     Running   0          45s
osticket-service-distributor-*-*          1/1     Running   0          45s
```

Now start an evaluation and wait for the ticket to be created. Note: You must have your services tagged with `keptn_project`, `keptn_service` and `keptn_stage`.
```
keptn send event start-evaluation --project=*** --stage=*** --service=*** --timeframe=2m
```

# Debugging
All incoming events from Keptn to the service are logged in raw form to `/var/www/html/logs/osticketIncomingEvents.log` of the `osticket-service` pod.

```
kubectl exec -itn keptn osticket-service-*-* cat /var/www/html/logs/osticketIncomingEvents.log
```

## Compatibility Matrix

| Keptn Version    | [OSTicket Service for Keptn](https://hub.docker.com/r/adamgardnerdt/keptn-osticket-service) | OSTicket Version|
|:----------------:|:----------------------------------------:|:------------------------------------------------------------------:|
|       0.6.1      | adamgardnerdt/keptn-osticket-service |  v1.14.1 |

# Contributions, Enhancements, Issues or Questions
Please raise a GitHub issue or join the [Keptn Slack channel](https://join.slack.com/t/keptn/shared_invite/enQtNTUxMTQ1MzgzMzUxLWMzNmM1NDc4MmE0MmQ0MDgwYzMzMDc4NjM5ODk0ZmFjNTE2YzlkMGE4NGU5MWUxODY1NTBjNjNmNmI1NWQ1NGY).
