<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegiaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regiaos = [
            [
                "nome" => "Alagoinhas",
                "bloco_id" => 1,
            ],
            [
                "nome" => "CATU",
                "bloco_id" => 1,
            ],
            [
                "nome" => "ENTRE RIOS",
                "bloco_id" => 1,
            ],
            [
                "nome" => "ESPLANADA",
                "bloco_id" => 1,
            ],
            [
                "nome" => "RIBEIRA DO POMBAL",
                "bloco_id" => 1,
            ],
            [
                "nome" => "SANTA TEREZINHA",
                "bloco_id" => 1,
            ],
            [
                "nome" => "Barreiras",
                "bloco_id" => 2,
            ],
            [
                "nome" => "Barreiras II",
                "bloco_id" => 2,
            ],
            [
                "nome" => "Bom Jesus da Lapa",
                "bloco_id" => 2,
            ],
            [
                "nome" => "Ibotirama",
                "bloco_id" => 2,
            ],
            [
                "nome" => "Luis Eduardo",
                "bloco_id" => 2,
            ],
            [
                "nome" => "Camaçari",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Barra da Pojuca",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Dias D´Avila",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Lama Preta",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Monte Gordo",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Phoc",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Pojuca",
                "bloco_id" => 3,
            ],
            [
                "nome" => "Caminho de Areia",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Alto de Coutos",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Fazenda Coutos",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Ilha Amerela",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Lobato",
                "bloco_id" =>    4,
            ],
            [
                "nome" => "Mirante de Periperi",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Paripe",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Paripe II",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Plataforma",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Santa Luzia",
                "bloco_id" => 4,
            ],
            [
                "nome" => "Uruguai",
                "bloco_id" =>    4,
            ],
            [
                "nome" => "Cajazeiras X",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Aguas Claras",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Cajazeiras VI",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Castelo Branco",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Castelo Branco II",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Mussurunga",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Nova Brasilia",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Valeria",
                "bloco_id" => 5,
            ],
            [
                "nome" => "Candeias",
                "bloco_id" => 6,
            ],
            [
                "nome" => "Madre de Deus",
                "bloco_id" => 6,
            ],
            [
                "nome" => "Nova Candeias",
                "bloco_id" => 6,
            ],
            [
                "nome" => "São Franscico do Conde",
                "bloco_id" => 6,
            ],
            [
                "nome" => "Vitória da Conquista",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Bairro Brasil",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Brumado",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Caetite",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Candido Sales",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Guanambi",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Ipiau",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Itapetinga",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Jaguaquara",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Jequie",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Jequie IV",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Paramirim",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Pocoes",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Urbis VI",
                "bloco_id" => 7,
            ],
            [
                "nome" => "Dois Leões",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Aquidaba",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Brotas",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Cine Centro",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Cosme de Farias",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Eng Velho de Brotas",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Federação",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Liberdade",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Matatu de Brotas",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Pau da Lima",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Vale da Muriçoca",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Vale do Matatu",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Vasco da Gama",
                "bloco_id" => 8,
            ],
            [
                "nome" => "Fazenda Grande",
                "bloco_id" => 9,
            ],
            [
                "nome" => "Campinas de Piraja",
                "bloco_id" => 9,
            ],
            [
                "nome" => "Largo do Tanque",
                "bloco_id" => 9,
            ],
            [
                "nome" => "Piraja do Lobato",
                "bloco_id" => 9,
            ],
            [
                "nome" => "San Martin",
                "bloco_id" => 9,
            ],
            [
                "nome" => "São Caetano",
                "bloco_id" => 9,
            ],
            [
                "nome" => "Feira de Santana",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Amelia Rodrigues",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Campo Limpo",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Conceição do Coite",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Conceição do Jacuípe",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Ipira",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Irece",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Jacobina",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Presidente Dutra",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Santo Estevão",
                "bloco_id" => 10,
            ],
            [
                "nome" => "São Gonçalos dos Campos",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Xique Xique",
                "bloco_id" => 10,
            ],
            [
                "nome" => "Feira de Santana II",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Cachoeira",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Cansanção",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Euclides da Cunha",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Itaberaba",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Muritiba",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Serrinha",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Tomba",
                "bloco_id" => 11,
            ],
            [
                "nome" => "Itabuna",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Itabuna II",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Itabuna III",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Ilheus",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Canavieras",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Lomanto",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Malhado",
                "bloco_id" => 12,
            ],
            [
                "nome" => "Itapuã",
                "bloco_id" => 13,
            ],
            [
                "nome" => "Avenida Aliomar",
                "bloco_id" => 13,
            ],
            [
                "nome" => "Bairro da Paz",
                "bloco_id" => 13,
            ],
            [
                "nome" => "Boca do Rio",
                "bloco_id" => 13,
            ],
            [
                "nome" => "Boca do Rio II",
                "bloco_id" => 13,
            ],
            [
                "nome" => "Imbui",
                "bloco_id" => 13,
            ],
            [
                "nome" => "São Cristovão",
                "bloco_id" => 13,
            ],
            [
                "nome" => "Juazeiro",
                "bloco_id" => 14,
            ],
            [
                "nome" => "Bonfim",
                "bloco_id" => 14,
            ],
            [
                "nome" => "Paulo Afonso",
                "bloco_id" => 14,
            ],
            [
                "nome" => "Remanso",
                "bloco_id" => 14,
            ],
            [
                "nome" => "Pernambues",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Barra",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Itaparica",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Mar Grande",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Nordeste",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Pituba",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Santa Cruz",
                "bloco_id" => 15,
            ],
            [
                "nome" => "São Rafael",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Sete de Abril",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Vale das Pedrinhas",
                "bloco_id" => 15,
            ],
            [
                "nome" => "Santo Antônio de Jesus",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Amargosa",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Castro Alves",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Cruz das Almas",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Gandu",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Nazare",
                "bloco_id" => 16,
            ],
            [
                "nome" => "São Benedito",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Valença",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Valença II Bolivia",
                "bloco_id" => 16,
            ],
            [
                "nome" => "Simões Filho",
                "bloco_id" => 17,
            ],
            [
                "nome" => "Areia Branca II",
                "bloco_id" => 17,
            ],
            [
                "nome" => "CIA",
                "bloco_id" => 17,
            ],
            [
                "nome" => "Santo Amaro",
                "bloco_id" => 17,
            ],
            [
                "nome" => "Saubara",
                "bloco_id" => 17,
            ],
            [
                "nome" => "Tancredo Neves",
                "bloco_id" => 18,
            ],
            [
                "nome" => "Engomadeira",
                "bloco_id" => 18,
            ],
            [
                "nome" => "Estrada das Barreiras",
                "bloco_id" => 18,
            ],
            [
                "nome" => "Sussuarana",
                "bloco_id" => 18,
            ],
            [
                "nome" => "Teixeira de Freitas",
                "bloco_id" => 19,
            ],
            [
                "nome" => "Baianao II",
                "bloco_id" => 19,
            ],
            [
                "nome" => "Eunapolis",
                "bloco_id" => 19,
            ],
            [
                "nome" => "Itamaraju",
                "bloco_id" => 19,
            ],
            [
                "nome" => "Porto Seguro",
                "bloco_id" => 19,
            ],
            [
                "nome" => "Praca da Biblia",
                "bloco_id" => 19,
            ],
            [
                "nome" => "Vilas do Atlântico",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Itinga",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Jaua",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Lauro de Freitas",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Parque Santa Rita",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Vida Nova",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Vila de Abrante",
                "bloco_id" => 20,
            ],
            [
                "nome" => "Catedral",
                "bloco_id" => 21,
            ],

        ];
        DB::table('regiaos')->insert($regiaos);
    }
}
