# OSTicket Service
This service creates tickets in [OSTicket](https://github.com/osTicket/osTicket) when a keptn evaluation (`sh.keptn.event.start-evaluation`) is performed. The service is subscribed to the following keptn events:

* sh.keptn.events.evaluation-done

# Installation
To use this service, you need a running OSTicket system. If you need to create one, use the `osTicketInstall.sh` file in the `osticket-setup-files` folder.

> This OSTicket installation script is NOT secure and meant only for demo purposes.

You will require an OSTicket API key with `Create Ticket` permissions.

1. Go to `http://OSTICKET-IP/scp/apikeys.php?a=add` generate an API key (use the Keptn IP).
2. Adjust the `OSTICKET_URL` and `OSTICKET_API_KEY` values in `osticket-service.yaml` to reflect your values.
3. Use kubectl to apply both the `osticket-service.yaml` and `osticket-distributor.yaml` files on the keptn cluster:

```
kubectl apply -f osticket-service -f osticket-distributor.yaml
```

Expected output:
```
kubectl -n keptn get pods | grep osticket
```

# Verification of Installation
```
kubectl -n keptn get pods | grep osticket
```
# Contributions, Enhancements, Issues or Questions
Please raise a GitHub issue.
