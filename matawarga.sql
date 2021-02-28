CREATE DATABASE IF NOT EXISTS `matawarga` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `matawarga`;
CREATE TABLE `gambar_kejadian` (
`idgambar` int(11) NOT NULL,
`idkejadian` int(11) NOT NULL,
`extension` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; CREATE TABLE `kejadian` (
`idkejadian` int(11) NOT NULL,
`username` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL, `judul` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL, `deskripsi` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
`instansi_tujuan` enum('PEMKOT','PLN','PDAM','POLISI','Lain-lain') COLLATE utf8mb4_unicode_ci NOT NULL,
`tanggal` datetime NOT NULL,
`longitude` double NULL, `latitude` double NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; CREATE TABLE `komen_kejadian` (
`idkomen_kejadian` int(11) NOT NULL,
`idkejadian` int(11) NOT NULL,
`username` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL, `komentar` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `like_kejadian` (
`idkejadian` int(11) NOT NULL,
`username` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; CREATE TABLE `user` (
`username` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL, `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL, `password` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL, `salt` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `gambar_kejadian` ADD PRIMARY KEY (`idgambar`), ADD KEY `idkejadian` (`idkejadian`);
ALTER TABLE `kejadian` ADD PRIMARY KEY (`idkejadian`), ADD KEY `username` (`username`); ALTER TABLE `komen_kejadian`
ADD PRIMARY KEY (`idkomen_kejadian`),
ADD KEY `idkejadian` (`idkejadian`), ADD KEY `username` (`username`);
ALTER TABLE `like_kejadian` ADD KEY `idkejadian` (`idkejadian`), ADD KEY `like_kejadian_ibfk_1` (`username`);
ALTER TABLE `user` ADD PRIMARY KEY (`username`);
ALTER TABLE `gambar_kejadian` MODIFY `idgambar` int(11) NOT NULL AUTO_INCREMENT; ALTER TABLE `kejadian` MODIFY `idkejadian` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `komen_kejadian` MODIFY `idkomen_kejadian` int(11) NOT NULL AUTO_INCREMENT; ALTER TABLE `gambar_kejadian`
ADD CONSTRAINT `gambar_kejadian_ibfk_1` FOREIGN KEY (`idkejadian`) REFERENCES `kejadian` (`idkejadian`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `kejadian` ADD CONSTRAINT `kejadian_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user` (`username`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `komen_kejadian`
ADD CONSTRAINT `komen_kejadian_ibfk_1` FOREIGN KEY (`idkejadian`) REFERENCES `kejadian`
(`idkejadian`) ON DELETE RESTRICT ON UPDATE RESTRICT,
ADD CONSTRAINT `komen_kejadian_ibfk_2` FOREIGN KEY (`username`) REFERENCES `user`
(`username`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `like_kejadian`
ADD CONSTRAINT `like_kejadian_ibfk_1` FOREIGN KEY (`username`) REFERENCES `user`
(`username`) ON DELETE RESTRICT ON UPDATE RESTRICT,
ADD CONSTRAINT `like_kejadian_ibfk_2` FOREIGN KEY (`idkejadian`) REFERENCES `kejadian`
(`idkejadian`) ON DELETE RESTRICT ON UPDATE RESTRICT;