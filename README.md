# MiniPM

Mini projektmenedzsment alkalmazás: csapatok, projektek, feladatok és felhasználók kezelése, projekt részletező oldal státusz szerint csoportosított feladatokkal, és csapat szintű statisztikák.

**Stack:** Symfony 8.1 · PHP 8.4 · Doctrine ORM 3 · MySQL 8.4 (Docker) · EasyAdmin 5 · Twig · Bootstrap 5.3

## Követelmények

- PHP 8.4 (`intl` kiterjesztéssel) és Composer
- [Symfony CLI](https://symfony.com/download)
- Docker (csak az adatbázishoz)
- `make`

A projekt gyökerében lévő `.php-version` fájl miatt a Symfony CLI a PHP 8.4-et használja.

## Indítás

```bash
composer install
make start       # MySQL konténer + Symfony szerver háttérben
make migrate     # adatbázis séma
make fixtures    # demo adatok
```

Ezután: <https://127.0.0.1:8000/admin>

Ha a böngésző a tanúsítványra figyelmeztet, egyszer futtasd le a `symfony server:ca:install` parancsot.

### Belépési adatok

Minden demo fiók jelszava: **`password`**

| E-mail | Szerepkör |
|---|---|
| `admin@example.com` | Adminisztrátor |
| `member@example.com` | Tag |

A demo adat 7 felhasználót, 3 csapatot, 8 projektet és 23 feladatot tartalmaz, a határidők a mai naphoz viszonyítva (mindig van lejárt és közelgő feladat).

## Makefile parancsok

| Parancs | Leírás |
|---|---|
| `make start` / `make stop` | adatbázis és webszerver indítása / leállítása |
| `make up` / `make down` | csak az adatbázis konténer |
| `make log` | a webszerver naplója |
| `make migration` / `make migrate` | migráció generálása / futtatása |
| `make fixtures` | demo adatok újratöltése |
| `make test` | teszt adatbázis újraépítése (migrációk + fixture-ök), majd PHPUnit |
| `make stan` | PHPStan (level 8) |
| `make cs` / `make cs-fix` | PHP CS Fixer ellenőrzés / javítás (`@Symfony` szabályok) |
| `make lint` | YAML, Twig, konténer és Doctrine séma ellenőrzés |
| `make check` | `cs` + `stan` + `lint` + `test` |
