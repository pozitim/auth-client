Turkcell oturum anahtarının elde edilmesi için SessionClientImpl ve SessionCacheClientImpl isimli sınıflar kullanılmalıdır.

Memcache desteği olmadan SessionClientImpl aşağıdaki şekilde kullanılabilir:
```php
$logger = new Logger();
$sessionClient = new \Pozitim\AuthClient\Turkcell\SessionClientImpl($logger);
$sessionClient->setApiEndpoint('http://auth.domain.com/api');
$sessionClient->setTurkcellEndpoint(''); // API dökümanına göz atılabilir.
$sessionClient->setServiceVariantId(123456);
$sessionClient->setSpId(1234);
$sessionClient->setPassword(12345678);
$sessionId = $sessionClient->getSessionId();
```

Eğer uygulamada sürekli auth projesine gidilmesi performans sorunu yaratacaksa SessionCacheClientImpl sınıfı kullanılabilir. Bu sınıf SessionClientImpl ve memcached objeleri kullanılarak üretilebilir.
```php
$memcached = new Memcached();
$sessionCacheClient = new \Pozitim\AuthClient\Turkcell\SessionCacheClientImpl($sessionClient, $memcached);
$sessionId = $sessionCacheClient->getSessionId();
```

Alınan oturum anahtarı ilgili yerlerde kullanıldığında eğer Turkcell tarafından oturum anahtarı geçersiz hatası alınırsa uygulama yaşam döngüsünde yardımcı sınıf kullanılarak oturum anahtarı aşağıdaki gibi resetlenmeli ve tekrar sessionID istenmelidir:

```php
<?php
$sessionClient->reset();
$sessionId = $sessionClient->getSessionId();
```