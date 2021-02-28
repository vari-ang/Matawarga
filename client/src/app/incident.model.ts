export class IncidentModel {
    constructor(
        public idkejadian: number, 
        public username: string, 
        public judul: string, 
        public deskripsi: string,
        public instansi_tujuan:string,
        public liked:boolean,
        public jumlah_like:number,
        public komens,
        public jumlah_komen:number,
        public tanggal:string,
        public longitude:number,
        public latitude:number,
    ) {}
}