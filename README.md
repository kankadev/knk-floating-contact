# KNK Floating Contact

Ein kleines WordPress-Plugin von [kanka.dev](https://kanka.dev) für schwebende E-Mail-, Telefon- und Buchungslinks mit eigenen SVG-Icons und optionaler WPML-Übersetzung.

## Installation und Updates

Voraussetzungen: WordPress 5.8 oder neuer, PHP 7.4 oder neuer.

1. Das ZIP `knk-floating-contact-VERSION.zip` aus einem [GitHub Release](https://github.com/kankadev/knk-floating-contact/releases) herunterladen. Die automatisch von GitHub erzeugten „Source code“-Archive sind keine Installationspakete.
2. In WordPress unter **Plugins → Installieren → Plugin hochladen** installieren beziehungsweise die vorhandene Version ersetzen. Nicht vorher deinstallieren.
3. Plugin aktivieren. Konfiguration unter **Einstellungen → KNK Floating Contact**.

Ab Version 1.1.0 prüft das aktive Plugin öffentliche stabile GitHub-Releases über den normalen WordPress-Updateprozess. Vorabversionen, Entwürfe, reine Tags und Releases ohne korrekt benanntes ZIP werden ignoriert. Die Abfrage wird sechs Stunden zwischengespeichert, Fehler 15 Minuten. GitHub erhält dabei die Server-IP und übliche HTTP-Metadaten, aber keine Kontaktoptionen und kein GitHub-Zugriffstoken. Netzwerk- oder API-Fehler verändern keine Einstellungen.

Automatische Updates lassen sich pro Installation in der WordPress-Pluginliste einschalten. Sie werden nicht vom Plugin erzwungen. Alte Versionen ohne Updater benötigen einmalig das neue Release-ZIP; deaktivierte Plugins führen ihren eigenen Updater nicht aus.

## Einstellungen bleiben pro Website erhalten

Alle bisherigen `knk_floating_contact_*`-Optionsnamen bleiben bestehen. Installation, Aktivierung, Deaktivierung und Updates schreiben keine Standardwerte über vorhandene Einstellungen. Das ZIP enthält ausschließlich Programmdateien. Individuelle E-Mail-Adressen, Rufnummern, Buchungslinks, Texte und Icons bleiben in der jeweiligen WordPress-Datenbank.

Nur eine tatsächliche Deinstallation mit aktivierter Option „Daten beim Deinstallieren löschen“ entfernt die 17 eigenen Optionen. Standard ist **Daten behalten**. In Multisite wird die Zustimmung jeder einzelnen Site berücksichtigt. Änderungen direkt in Plugin-Dateien werden bei Updates ersetzt: Anpassungen gehören in die Einstellungen, ein Child-Theme oder ein separates Erweiterungsplugin.

SVGs werden beim Speichern und Ausgeben auf passive Grafikformen begrenzt. Skripte, Event-Handler, eingebettetes HTML und externe Referenzen sind nicht erlaubt. Einfache Farbregeln wie `.cls-1 { fill: #fff; }` werden in sichere SVG-Attribute umgewandelt; Style-Blöcke werden nicht ausgegeben. Bei vorhandenen komplexen SVGs vor dem Update die Darstellung auf einer Testinstanz prüfen. Die gespeicherten Werte werden beim Update nicht umgeschrieben.

## Entwicklung und Veröffentlichung

- SCSS-Quelle: `assets/css/floating-contact.scss`. Die bestehenden Frontend-Klassennamen und das Frontend-JavaScript bleiben erhalten. Designoptionen verwenden CSS-Variablen; SCSS und kompiliertes CSS müssen gemeinsam gepflegt werden. Der Release-Build kompiliert kein Sass.
- Version im Plugin-Header, der Versionskonstante und `readme.txt` gemeinsam erhöhen; Änderungen im Changelog und in den Release Notes beschreiben.
- Mindestversionen bei Änderungen auch im Updater anpassen; Migrationen müssen bestehende Optionen erhalten.
- PHP-Syntax prüfen und `php tests/run.php /pfad/zu/wordpress` ausführen. Der Test benutzt echte WordPress-KSES-Funktionen, aber keine Datenbank.
- `python scripts/build-release.py v1.3.0` erstellt das installierbare ZIP und SHA256SUMS aus einer festen Dateiauswahl.
- Der GitHub-Workflow „Release“ läuft manuell mit einem Tag wie `v1.1.1` und dem vollständigen geprüften Commit-SHA auf `main`. Er testet, baut das ZIP und erstellt zunächst einen Release-Entwurf. Bestehende Tags werden nicht überschrieben.
- ZIP und Versionsstand prüfen, Release Notes fertigstellen und den Entwurf veröffentlichen. Erst ein veröffentlichtes stabiles Release wird von den Installationen angeboten. Ein Commit auf `main` allein verteilt kein Update.
- Vor Veröffentlichung immer Geheimnisprüfung und Diff-Review durchführen. Keine Site-Backups, Datenbankexporte oder echten Kundenoptionen in dieses Repository aufnehmen.

## Rückfall

Vor Updates die Plugin-Dateien und Datenbank sichern. Für einen Rückfall das vorherige Release-ZIP über „Plugin hochladen“ ersetzen, ohne zu deinstallieren. Einstellungen werden nicht aus einer anderen Instanz übernommen. Automatische Updates bei einem geplanten Rückfall gegebenenfalls zunächst ausschalten.

## Bekannte Grenzen

Breiten und Positionen sind weiterhin fest vorgegeben. Farben und Eckenrundung sind ab 1.3.0 einstellbar. Tastaturfokus erweitert die Texte bislang nicht wie Maus-Hover; Bewegungsreduktion und Reaktion auf nachträgliche Größenänderungen sind weitere mögliche Verbesserungen. Auch Version 1.2.0 erhält bewusst das bisherige Erscheinungsbild. Der eigene spätere Funktionsausbau ist davon unabhängig.

Lizenz: GPL-2.0-or-later.


## Kontaktarten ein- und ausblenden

Unter **Einstellungen → KNK Floating Contact** stehen E-Mail, Telefon und Buchung
jeweils in einer eigenen Gruppe. Der Schalter „… anzeigen“ blendet die Kontaktart
aus oder ein. Adressen, Texte und SVGs bleiben beim Ausschalten gespeichert.
Ohne Zieladresse erscheint auch ein eingeschalteter Kontakt nicht.
Bei Updates bestehender Installationen sind alle drei Kontaktarten weiterhin aktiv,
solange sie nicht ausdrücklich ausgeschaltet wurden. Es werden keine Standardwerte
in vorhandene Einstellungen geschrieben. Die Buchungs-URL hat ein breites,
mehrzeiliges Feld; visuelle Zeilenumbrüche verändern den Link nicht.

WPML ist **nicht erforderlich**. Ohne WPML wird der gespeicherte Buchungstext
unverändert verwendet; mit WPML bleibt die bestehende Übersetzungseinbindung aktiv.
Die SVG-Behandlung aus 1.1.0 wurde für 1.2.0 nicht verändert. Die vorhandenen
Icon-Positionen bleiben ebenfalls erhalten, wenn einzelne Kontakte ausgeblendet sind.
Multisite bleibt im bestehenden Umfang unterstützt; die Tests der Deinstallation
prüfen die Löschfreigabe je Site, ersetzen aber keine vollständige Netzwerkabnahme.


## Updatesichere Gestaltung ab 1.3.0

Die Gruppe **Gestaltung** steuert Hintergrund, Desktop-Hover-Hintergrund, Textfarbe,
optionale SVG-Icon-Farbe und Eckenrundung gemeinsam für alle drei Kontaktarten.
Farben können per WordPress-Farbauswahl oder als Hexwert eingegeben werden.
Leere Felder verwenden das bisherige Standarddesign: Hintergrund `#6C1300`,
Hover `#74675C`, Text weiß, eckige Form. Die ursprünglichen SVG-Farben bleiben
erhalten, solange die optionale Icon-Farbe leer ist. „Leeren“ und Speichern
stellt den jeweiligen Standard wieder her, ohne Kontaktdaten zu löschen.

Die Icon-Farbe ersetzt ausschließlich deckende explizite SVG-Farbflächen und Linien
mit Hex- oder benannten Farben; `currentColor` übernimmt ebenfalls die Auswahl.
Unbemalte Flächen (`none`), Transparenz, Alpha-Farbwerte, Farbverläufe und Geometrie
bleiben erhalten. Andere komplexe Farbdefinitionen bleiben unverändert. Emojis
lassen sich auf diesem Weg nicht umfärben. Die gewählte Farbe wird nur bei der Ausgabe angewendet; Leeren stellt die Originalfarben wieder her.
Beim Speichern greift weiterhin der vorhandene SVG-Sicherheitsfilter. Er kann beispielsweise einfache Farbklassen in gleichwertige Attribute umwandeln.

Die Gestaltung liegt in der jeweiligen WordPress-Datenbank und bleibt bei Updates,
Deaktivieren und Reaktivieren erhalten. Kein Aktivierungs-/Update-Reset.

**Vor dem ersten Update bisher manuell angepasster Installationen:** Plugin-Ordner
sichern und eigene SCSS-/CSS-Werte in die neuen Einstellungen übertragen.
Beliebige vorhandene Dateiänderungen werden nicht automatisch importiert und würden
beim Update ersetzt. Weitergehende Anpassungen gehören in ein Child-Theme oder
ein eigenes Erweiterungsplugin, nicht in diesen Plugin-Ordner.

Der Footer der Einstellungsseite zeigt einen Link zu kanka.dev und die tatsächlich
geladene Plugin-Version. Er lädt keine externen Schriftarten, Bilder oder Skripte.

Ab 1.3.1 bilden ausschließlich sichtbare Kontakte eine vertikal zentrierte Gruppe mit 15 px Abstand. Deaktivierte Kontakte hinterlassen keine Leerplätze.
