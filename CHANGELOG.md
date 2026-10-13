# 1.3.0

- Updatesichere Designoptionen für Hintergrund, Hover-Hintergrund, Textfarbe,
  optionale SVG-Icon-Farbe und Eckenrundung.
- WordPress-Farbauswahl nur auf der eigenen Einstellungsseite.
- Bisheriges Design ohne eigene Angaben unverändert; keine Migration oder Resets.
- Optionale Icon-Farbe erhält Geometrie, Transparenz und Verläufe; gespeicherte SVGs bleiben unverändert.
- Footer mit kanka.dev-Link und aktueller Plugin-Version.
- Vor Updates individuell geänderter Plugin-Dateien die bisherigen Anpassungen sichern
  und in Einstellungen beziehungsweise Child-Theme übertragen.

# 1.2.0

- Einstellungen nach E-Mail, Telefon und Buchung gruppiert.
- Jede Kontaktart separat ein-/ausblendbar; gespeicherte Angaben bleiben erhalten.
- Bestehende Installationen behalten ohne explizites Abschalten ihre sichtbaren Kontakte.
- Breite Eingabefelder und mehrzeilige Buchungs-URL mit rein visuellem Zeilenumbruch.
- SVG-Filter und Frontend-Assets aus 1.1.0 unverändert; WPML bleibt optional.
- Tests für alle acht Schalterkombinationen, Standardwerte, Betrieb ohne WPML und
  Bereinigung der neuen Optionen bei freigegebener Deinstallation.

# Änderungen

## 1.1.0

- Stabile öffentliche GitHub Releases als WordPress-Updatequelle; installierbare ZIP-Dateien und reproduzierbarer Paketbau.
- SVG-Filter beim Speichern und Ausgeben; Berechtigungsprüfung der Einstellungsseite.
- Zugängliche Linknamen auch bei ausgeblendeten mobilen Texten.
- Vorhandenen Buchungstext mit unverändertem Kontext bei WPML registrieren.
- Multisite-Deinstallation berücksichtigt die Löschfreigabe jeder Site und stellt den ursprünglichen Site-Kontext wieder her.
- Alle bisherigen Optionsnamen und gespeicherten Werte bleiben bei Updates erhalten.
- Bestehendes CSS, SCSS und JavaScript unverändert.

## 1.0.1

Vorhandener Stand mit E-Mail-, Telefon- und Buchungslink sowie optionaler Datenlöschung bei Deinstallation.
