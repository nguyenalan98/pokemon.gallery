/* TODO: create tables */

CREATE TABLE login(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username text UNIQUE NOT NULL,
  password text NOT NULL,
  session int
);

CREATE TABLE tags(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name text UNIQUE NOT NULL
);

CREATE TABLE photos(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  photo text NOT NULL,
  uploader_name text
);

CREATE TABLE taglist(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  imageid int,
  tagid int,
  FOREIGN KEY (imageid) REFERENCES photos(id),
  FOREIGN KEY (tagid) REFERENCES tags(id)
);

/* TODO: initial seed data */

INSERT INTO login (username, password) VALUES ('seedData','$2y$10$EwIUIZbblZqolC3AkM6u.ejxgWjk0yGf9Btm.noTCAtKTKiNvqwL2');/*pass:12345*/
INSERT INTO login (username, password) VALUES ('alan', '$2y$10$AiYhZKMPrkPzx0aYyU/IOOaMiTCduvNMpuB/1YD1wgMdILxk7EDrG');/*pass:info2300*/

INSERT INTO tags (name,id) VALUES ('flying', 0);
INSERT INTO tags (name) VALUES ('ground');
INSERT INTO tags (name) VALUES ('cute');
INSERT INTO tags (name) VALUES ('water');
INSERT INTO tags (name) VALUES ('fire');
INSERT INTO tags (name) VALUES ('earth');
INSERT INTO tags (name) VALUES ('not cute');
INSERT INTO tags (name) VALUES ('dragon');
INSERT INTO tags (name) VALUES ('fluffy');

INSERT INTO photos (photo,uploader_name, id) VALUES ('Abomasnow.jpg',"seedData", 0);
INSERT INTO photos (photo,uploader_name) VALUES ('Blastoise.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Bulbasaur.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Charizard.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Dragonite.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Giratina.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Gyarados.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Kyurem.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Lugia.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Aggron.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Arcanine.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Feraligatr.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Flygon.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Meganium.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Steelix.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Typhlosion.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Yveltal.jpg',"seedData");
INSERT INTO photos (photo,uploader_name) VALUES ('Noivern.jpg',"seedData");

INSERT INTO taglist (imageid,tagid) VALUES (0,3);
INSERT INTO taglist (imageid,tagid) VALUES (0,1);
INSERT INTO taglist (imageid,tagid) VALUES (0,2);
INSERT INTO taglist (imageid,tagid) VALUES (0,8);

INSERT INTO taglist (imageid,tagid) VALUES (10,4);
INSERT INTO taglist (imageid,tagid) VALUES (10,2);
INSERT INTO taglist (imageid,tagid) VALUES (10,8);

INSERT INTO taglist (imageid,tagid) VALUES (1,2);
INSERT INTO taglist (imageid,tagid) VALUES (1,1);
INSERT INTO taglist (imageid,tagid) VALUES (1,3);

INSERT INTO taglist (imageid,tagid) VALUES (2,1);
INSERT INTO taglist (imageid,tagid) VALUES (2,5);

INSERT INTO taglist (imageid,tagid) VALUES (3,4);
INSERT INTO taglist (imageid,tagid) VALUES (3,0);
INSERT INTO taglist (imageid,tagid) VALUES (3,7);

INSERT INTO taglist (imageid,tagid) VALUES (4,0);
INSERT INTO taglist (imageid,tagid) VALUES (4,2);
INSERT INTO taglist (imageid,tagid) VALUES (4,7);

INSERT INTO taglist (imageid,tagid) VALUES (7,7);

INSERT INTO taglist (imageid,tagid) VALUES (8,0);

INSERT INTO taglist (imageid,tagid) VALUES (11,1);
INSERT INTO taglist (imageid,tagid) VALUES (11,3);
