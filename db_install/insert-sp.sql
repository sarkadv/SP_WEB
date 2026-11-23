INSERT INTO PRAVO (nazev)
VALUES ('admin');

INSERT INTO PRAVO (nazev)
VALUES ('spravce');

INSERT INTO PRAVO (nazev)
VALUES ('zakaznik');

INSERT INTO MESTO (nazev, psc)
VALUES ('Citadel', '10101');

INSERT INTO MESTO (nazev, psc)
VALUES ('Illium', '11111');

INSERT INTO MESTO (nazev, psc)
VALUES ('Eden Prime', '00100');

INSERT INTO MESTO (nazev, psc)
VALUES ('Megaton', '89390');

INSERT INTO ADRESA (ulice, planeta, c_mesta_fk)
VALUES ('Green St. 8', 'Mars', '3');

INSERT INTO ADRESA (ulice, planeta, c_mesta_fk)
VALUES ('Blue St. 13', 'Pluto', '2');

INSERT INTO ADRESA (ulice, planeta, c_mesta_fk)
VALUES ('Earthly 120', 'Artemis Tau', '1');

INSERT INTO ADRESA (ulice, planeta, c_mesta_fk)
VALUES ('Alien St. 820', 'Artemis Tau', '1');

INSERT INTO ADRESA (ulice, planeta, c_mesta_fk)
VALUES ('Weird St. 23', 'Měsíc', '4');

INSERT INTO UZIVATEL (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)
VALUES ('admin@gmail.com', '$2y$10$3ilq9lz/hBvswJw11SOU5uegINH5lRbk2lA0Ogctf8dj7NY8nO5Ca', 'admin', 'admin', '2001-10-16', '010020030', '1', '1');

INSERT INTO UZIVATEL (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)
VALUES ('spravce@gmail.com', '$2y$10$2Hndz4V0kqsOAN5by1lA..EWY1kVI1ubziD0zzjJrymbHTAIVCPj.', 'spravce', 'spravce', '2001-10-16', '101110011', '2', '2');

INSERT INTO UZIVATEL (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)
VALUES ('ufon@gmail.com', '$2y$10$q8LipmICeK/6xjzxpqtrY.w6KmhfcmnGFLrm1rV2ybGWzTEWcNQM6', 'Garrus', 'Vakarian', '3001-10-16', '111000111', '3', '3');

INSERT INTO UZIVATEL (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)
VALUES ('liara@gmail.com', '2y$10$CC4aDjzUgYJUhtlvbLNbc.UJPmpX8/u.2VRutw1vNceb8f1CqmVYS', 'Liara', 'TSoni', '3000-9-16', '123091190', '3', '4');

INSERT INTO UZIVATEL (email, heslo, jmeno, prijmeni, d_narozeni, tel_cislo, c_prava_fk, c_adresy_fk)
VALUES ('mordin@gmail.com', '2y$10$FvTcTY5nlBuXzF534zIG1O9HwGrWDb0T.5Wf/Qci92IIde1N3MRr6', 'Mordin', 'Solus', '3000-2-27', '097472969', '3', '4');

INSERT INTO MODEL (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)
VALUES ('The Timeless Classic', '20000', '2', '68', '2000', 'Klasické UFO pro každou příležitost.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus varius ultricies sapien, vel consequat neque fermentum eget. Etiam vel libero enim. Etiam tempor ornare ante, et cursus enim tincidunt eget. Quisque sollicitudin risus nec ex malesuada, et pretium ante sagittis. Nunc feugiat, ante vel molestie cursus, mauris urna vulputate magna, ut blandit ipsum dui quis ex. Nam sit amet massa dignissim, elementum lectus quis, dignissim lorem. Curabitur blandit placerat lorem in tincidunt. Maecenas fermentum libero ac ex ornare lobortis.', 'img/the_timeless_classic.png');

INSERT INTO MODEL (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)
VALUES ('The Cubicle', '30000', '3', '73', '2400', 'Moderní UFO v hranatém provedení.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus varius ultricies sapien, vel consequat neque fermentum eget. Etiam vel libero enim. Etiam tempor ornare ante, et cursus enim tincidunt eget. Quisque sollicitudin risus nec ex malesuada, et pretium ante sagittis. Nunc feugiat, ante vel molestie cursus, mauris urna vulputate magna, ut blandit ipsum dui quis ex. Nam sit amet massa dignissim, elementum lectus quis, dignissim lorem. Curabitur blandit placerat lorem in tincidunt. Maecenas fermentum libero ac ex ornare lobortis.', 'img/the_cubicle.png');

INSERT INTO MODEL (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)
VALUES ('Škoda 4000', '12000', '4', '36', '1300', 'Nejnovější model od pozemské společnosti Škoda.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus varius ultricies sapien, vel consequat neque fermentum eget. Etiam vel libero enim. Etiam tempor ornare ante, et cursus enim tincidunt eget. Quisque sollicitudin risus nec ex malesuada, et pretium ante sagittis. Nunc feugiat, ante vel molestie cursus, mauris urna vulputate magna, ut blandit ipsum dui quis ex. Nam sit amet massa dignissim, elementum lectus quis, dignissim lorem. Curabitur blandit placerat lorem in tincidunt. Maecenas fermentum libero ac ex ornare lobortis.', 'img/skoda_4000.png');

INSERT INTO MODEL (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)
VALUES ('The Triplets', '60000', '9', '60', '1900', 'Pro větší skupiny cestovatelů.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus varius ultricies sapien, vel consequat neque fermentum eget. Etiam vel libero enim. Etiam tempor ornare ante, et cursus enim tincidunt eget. Quisque sollicitudin risus nec ex malesuada, et pretium ante sagittis. Nunc feugiat, ante vel molestie cursus, mauris urna vulputate magna, ut blandit ipsum dui quis ex. Nam sit amet massa dignissim, elementum lectus quis, dignissim lorem. Curabitur blandit placerat lorem in tincidunt. Maecenas fermentum libero ac ex ornare lobortis.', 'img/the_triplets.png');

INSERT INTO MODEL (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)
VALUES ('The Human UFO', '18000', '5', '57', '1600', 'UFO na lidský způsob.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus varius ultricies sapien, vel consequat neque fermentum eget. Etiam vel libero enim. Etiam tempor ornare ante, et cursus enim tincidunt eget. Quisque sollicitudin risus nec ex malesuada, et pretium ante sagittis. Nunc feugiat, ante vel molestie cursus, mauris urna vulputate magna, ut blandit ipsum dui quis ex. Nam sit amet massa dignissim, elementum lectus quis, dignissim lorem. Curabitur blandit placerat lorem in tincidunt. Maecenas fermentum libero ac ex ornare lobortis.', 'img/the_human_ufo.png');

INSERT INTO MODEL (nazev, cena_den, pocet_osob, vydrz_baterie, rychlost_ly, popis_kratky, popis_dlouhy, obrazek_url)
VALUES ('The Budget Option', '4000', '1', '10', '900', 'Vozidlo pro nenáročné a nemajetné.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus varius ultricies sapien, vel consequat neque fermentum eget. Etiam vel libero enim. Etiam tempor ornare ante, et cursus enim tincidunt eget. Quisque sollicitudin risus nec ex malesuada, et pretium ante sagittis. Nunc feugiat, ante vel molestie cursus, mauris urna vulputate magna, ut blandit ipsum dui quis ex. Nam sit amet massa dignissim, elementum lectus quis, dignissim lorem. Curabitur blandit placerat lorem in tincidunt. Maecenas fermentum libero ac ex ornare lobortis.', 'img/the_budget_option.png');

INSERT INTO UFO (c_modelu_fk)
VALUES ('1');

INSERT INTO UFO (c_modelu_fk)
VALUES ('1');

INSERT INTO UFO (c_modelu_fk)
VALUES ('1');

INSERT INTO UFO (c_modelu_fk)
VALUES ('2');

INSERT INTO UFO (c_modelu_fk)
VALUES ('2');

INSERT INTO UFO (c_modelu_fk)
VALUES ('3');

INSERT INTO UFO (c_modelu_fk)
VALUES ('3');

INSERT INTO UFO (c_modelu_fk)
VALUES ('4');

INSERT INTO UFO (c_modelu_fk)
VALUES ('4');

INSERT INTO UFO (c_modelu_fk)
VALUES ('5');

INSERT INTO UFO (c_modelu_fk)
VALUES ('5');

INSERT INTO UFO (c_modelu_fk)
VALUES ('5');

INSERT INTO UFO (c_modelu_fk)
VALUES ('6');

INSERT INTO UFO (c_modelu_fk)
VALUES ('6');

INSERT INTO UFO (c_modelu_fk)
VALUES ('6');

INSERT INTO UFO (c_modelu_fk)
VALUES ('6');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-11-12', '2022-11-13', '345987345019', '3', '4', '3');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-11-12', '2022-11-14', '123000245187', '3', '1', '3');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-11-24', '2022-11-30', '00039179077', '4', '5', '4');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-11-30', '2022-12-12', '00039179077', '4', '6', '4');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-12-13', '2022-12-15', '082619836982', '4', '7', '4');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-12-13', '2022-12-15', '9362936033', '5', '1', '5');

INSERT INTO VYPUJCKA (d_vypujceni, d_vraceni, c_platebniho_uctu, c_uzivatele_fk, c_ufo_fk, c_adresy_fk)
VALUES ('2022-12-15', '2022-12-17', '9362936033', '5', '6', '5');

INSERT INTO RECENZE (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)
VALUES('Ideální na delší výlety ve dvou po naší galaxii. Dostatečná rychlost i výdrž baterky.', '4', '2022-11-30 17:15:00', '1', '3');

INSERT INTO RECENZE (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)
VALUES('Nejlepší UFO, kterým jsem kdy letěl. Všem ho doporučuji.', '5', '2022-12-24 20:46:00', '1', '5');

INSERT INTO RECENZE (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)
VALUES('Model je velmi rychlý za dobrou cenu, ale kufr by mohl být větší.', '3', '2022-11-13 17:19:00', '2', '3');

INSERT INTO RECENZE (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)
VALUES('Divná barva.', '2', '2022-11-30 19:19:00', '2', '4');

INSERT INTO RECENZE (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)
VALUES('Pokud nejste nároční, toto je model pro vás. Já ale náročná jsem.', '1', '2022-12-20 10:23:00', '3', '4');

INSERT INTO RECENZE (text, hodnoceni, datum_cas, c_modelu_fk, c_uzivatele_fk)
VALUES('Lidé znovu ukázali, že UFA opravdu vyrábět neumí.', '2', '2022-12-20 10:23:00', '3', '5');
