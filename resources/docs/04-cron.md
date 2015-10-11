Cron
====

```txt
*  *  *  *  *  *
-  -  -  -  -  -
|  |  |  |  |  |
|  |  |  |  |  +---------- year [optional]
|  |  |  |  +------------- day of week (0 - 7) (Sunday=0 or 7)
|  |  |  +---------------- month (1 - 12)
|  |  +------------------- day of month (1 - 31)
|  +---------------------- hour (0 - 23)
+------------------------- min (0 - 59)
```

```yml
# file: resources/config/cron.yml
my_cron_task:
    description: Description
    rule: 0 */2 * * *
    callback: service_name:public_method
```
