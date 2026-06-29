<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IgrejaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $igrejas = [
            [
                "nome" => "Alagoinhas",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "Aracas",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "Barreiro",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "Inhambupe",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "Núcleo-Itapicuru",
                "bloco_id" => 1,
                "regiao_id" => 1,
                
            ],
            [
                "nome" => "Trab Especial-Lagoa Zona Rural",
                "bloco_id" => 1,
                "regiao_id" => 1,
                
            ],
            [
                "nome" => "Miguel Velho",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "Olindina",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "Petrolar",
                "bloco_id" => 1,
                "regiao_id" => 1,

            ],
            [
                "nome" => "CATU",
                "bloco_id" => 1,
                "regiao_id" => 2,

            ],
            [
                "nome" => "Catu II",
                "bloco_id" => 1,
                "regiao_id" => 2,

            ],
            [
                "nome" => "Catu III",
                "bloco_id" => 1,
                "regiao_id" => 2,

            ],
            [
                "nome" => "Trab Especial-Pedras",
                "bloco_id" => 1,
                "regiao_id" => 2,
                
            ],
            [
                "nome" => "ENTRERIOS",
                "bloco_id" => 1,
                "regiao_id" => 3,

            ],
            [
                "nome" => "Núcleo-Cardeal da Silva",
                "bloco_id" => 1,
                "regiao_id" => 3,
                
            ],
            [
                "nome" => "Núcleo-Ramos Machado",
                "bloco_id" => 1,
                "regiao_id" => 3,
                
            ],
            [
                "nome" => "ESPLANADA",
                "bloco_id" => 1,
                "regiao_id" => 4,

            ],
            [
                "nome" => "Acajutiba",
                "bloco_id" => 1,
                "regiao_id" => 4,

            ],
            [
                "nome" => "Conde",
                "bloco_id" => 1,
                "regiao_id" => 4,

            ],
            [
                "nome" => "Crisopolis",
                "bloco_id" => 1,
                "regiao_id" => 4,

            ],
            [
                "nome" => "Trab Especial-Jandaira",
                "bloco_id" => 1,
                "regiao_id" => 4,
                
            ],
            [
                "nome" => "Núcleo-Palame",
                "bloco_id" => 1,
                "regiao_id" => 4,
                
            ],
            [
                "nome" => "Rio Real",
                "bloco_id" => 1,
                "regiao_id" => 4,

            ],
            [
                "nome" => "Trab Especial-Sitio Crisopolis",
                "bloco_id" => 1,
                "regiao_id" => 4,
                
            ],
            [
                "nome" => "Núcleo-Vila do Conde",
                "bloco_id" => 1,
                "regiao_id" => 4,
                
            ],
            [
                "nome" => "Ribeira do Pombal",
                "bloco_id" => 1,
                "regiao_id" => 5,

            ],
            [
                "nome" => "Núcleo-Banzae",
                "bloco_id" => 1,
                "regiao_id" => 5,
                
            ],
            [
                "nome" => "Cicero Dantas",
                "bloco_id" => 1,
                "regiao_id" => 5,

            ],
            [
                "nome" => "Cipo",
                "bloco_id" => 1,
                "regiao_id" => 5,

            ],
            [
                "nome" => "Núcleo-Nova Soure",
                "bloco_id" => 1,
                "regiao_id" => 5,
                
            ],
            [
                "nome" => "Núcleo-Sitio do Quinto",
                "bloco_id" => 1,
                "regiao_id" => 5,
                
            ],
            [
                "nome" => "Santa Terezinha",
                "bloco_id" => 1,
                "regiao_id" => 6,

            ],
            [
                "nome" => "Núcleo-Aramari",
                "bloco_id" => 1,
                "regiao_id" => 6,
                
            ],
            [
                "nome" => "Núcleo-Freileao",
                "bloco_id" => 1,
                "regiao_id" => 6,
                
            ],
            [
                "nome" => "Núcleo-Satiro Dias",
                "bloco_id" => 1,
                "regiao_id" => 6,
                
            ],
            [
                "nome" => "Barreiras",
                "bloco_id" => 2,
                "regiao_id" => 7,

            ],
            [
                "nome" => "Bandeirantes",
                "bloco_id" => 2,
                "regiao_id" => 7,

            ],
            [
                "nome" => "Barreiras III",
                "bloco_id" => 2,
                "regiao_id" => 7,

            ],
            [
                "nome" => "Barreirinhas",
                "bloco_id" => 2,
                "regiao_id" => 7,

            ],
            [
                "nome" => "Núcleo-Cotegipe",
                "bloco_id" => 2,
                "regiao_id" => 7,
                
            ],
            [
                "nome" => "Núcleo-Riacho das Neves",
                "bloco_id" => 2,
                "regiao_id" => 7,
                
            ],
            [
                "nome" => "Santa Rita de Cassia",
                "bloco_id" => 2,
                "regiao_id" => 7,

            ],
            [
                "nome" => "Vila Brasil",
                "bloco_id" => 2,
                "regiao_id" => 7,

            ],
            [
                "nome" => "Barreiras II",
                "bloco_id" => 2,
                "regiao_id" => 8,

            ],
            [
                "nome" => "Formosa",
                "bloco_id" => 2,
                "regiao_id" => 8,

            ],
            [
                "nome" => "Bom Jesus da Lapa",
                "bloco_id" => 2,
                "regiao_id" => 9,

            ],
            [
                "nome" => "Cocos",
                "bloco_id" => 2,
                "regiao_id" => 9,

            ],
            [
                "nome" => "Correntina",
                "bloco_id" => 2,
                "regiao_id" => 9,

            ],
            [
                "nome" => "Núcleo-Riacho de Santana",
                "bloco_id" => 2,
                "regiao_id" => 9,
                
            ],
            [
                "nome" => "Santa Maria da Vitoria",
                "bloco_id" => 2,
                "regiao_id" => 9,

            ],
            [
                "nome" => "Núcleo-Santana",
                "bloco_id" => 2,
                "regiao_id" => 9,
                
            ],
            [
                "nome" => "Núcleo-Serra do Ramalho",
                "bloco_id" => 2,
                "regiao_id" => 9,
                
            ],
            [
                "nome" => "Serra Dourado",
                "bloco_id" => 2,
                "regiao_id" => 9,

            ],
            [
                "nome" => "Nucleo-Sitio do Mato",
                "bloco_id" => 2,
                "regiao_id" => 9,
                
            ],
            [
                "nome" => "Ibotirama",
                "bloco_id" => 2,
                "regiao_id" => 10,

            ],
            [
                "nome" => "Paratinga",
                "bloco_id" => 2,
                "regiao_id" => 10,

            ],
            [
                "nome" => "Luis Eduardo",
                "bloco_id" => 2,
                "regiao_id" => 11,

            ],
            [
                "nome" => "Acacias",
                "bloco_id" => 2,
                "regiao_id" => 11,

            ],
            [
                "nome" => "Mimoso",
                "bloco_id" => 2,
                "regiao_id" => 11,

            ],
            [
                "nome" => "Camaçari",
                "bloco_id" => 3,
                "regiao_id" => 12,

            ],
            [
                "nome" => "Núcleo-Pitanga de Palmares",
                "bloco_id" => 3,
                "regiao_id" => 12,
                
            ],
            [
                "nome" => "Barra da Pojuca",
                "bloco_id" => 3,
                "regiao_id" => 13,

            ],
            [
                "nome" => "Imbassai",
                "bloco_id" => 3,
                "regiao_id" => 13,

            ],
            [
                "nome" => "Malhadas",
                "bloco_id" => 3,
                "regiao_id" => 13,

            ],
            [
                "nome" => "Porto do Sauipe",
                "bloco_id" => 3,
                "regiao_id" => 13,

            ],
            [
                "nome" => "Praia do Forte",
                "bloco_id" => 3,
                "regiao_id" => 13,

            ],
            [
                "nome" => "Dias D´Avila",
                "bloco_id" => 3,
                "regiao_id" => 14,

            ],
            [
                "nome" => "Núcleo-Ama do Bahia",
                "bloco_id" => 3,
                "regiao_id" => 14,
                
            ],
            [
                "nome" => "Biribeira",
                "bloco_id" => 3,
                "regiao_id" => 14,

            ],
            [
                "nome" => "Dias Davila II",
                "bloco_id" => 3,
                "regiao_id" => 14,

            ],
            [
                "nome" => "Dias Davila III",
                "bloco_id" => 3,
                "regiao_id" => 14,

            ],
            [
                "nome" => "Núcleo-Itapecirica",
                "bloco_id" => 3,
                "regiao_id" => 14,
                
            ],
            [
                "nome" => "Mata de São João",
                "bloco_id" => 3,
                "regiao_id" => 14,

            ],
            [
                "nome" => "Lama Preta",
                "bloco_id" => 3,
                "regiao_id" => 15,

            ],
            [
                "nome" => "Cascalheira",
                "bloco_id" => 3,
                "regiao_id" => 15,

            ],
            [
                "nome" => "Parafuso",
                "bloco_id" => 3,
                "regiao_id" => 15,

            ],
            [
                "nome" => "Monte Gordo",
                "bloco_id" => 3,
                "regiao_id" => 16,

            ],
            [
                "nome" => "Barra do Jacuipe",
                "bloco_id" => 3,
                "regiao_id" => 16,

            ],
            [
                "nome" => "Phoc",
                "bloco_id" => 3,
                "regiao_id" => 17,

            ],
            [
                "nome" => "Gleba E",
                "bloco_id" => 3,
                "regiao_id" => 17,

            ],
            [
                "nome" => "Verde Horizonte",
                "bloco_id" => 3,
                "regiao_id" => 17,

            ],
            [
                "nome" => "Pojuca",
                "bloco_id" => 3,
                "regiao_id" => 18,

            ],
            [
                "nome" => "Los Angeles",
                "bloco_id" => 3,
                "regiao_id" => 18,

            ],
            [
                "nome" => "Caminho de Areia",
                "bloco_id" => 4,
                "regiao_id" => 19,

            ],
            [
                "nome" => "Boa Viagem",
                "bloco_id" => 4,
                "regiao_id" => 19,

            ],
            [
                "nome" => "Alto de Coutos",
                "bloco_id" => 4,
                "regiao_id" => 20,

            ],
            [
                "nome" => "Alto de Coutos II",
                "bloco_id" => 4,
                "regiao_id" => 20,

            ],
            [
                "nome" => "Constituinte",
                "bloco_id" => 4,
                "regiao_id" => 20,

            ],
            [
                "nome" => "Fazenda Coutos",
                "bloco_id" => 4,
                "regiao_id" => 21,

            ],
            [
                "nome" => "Casinhas",
                "bloco_id" => 4,
                "regiao_id" => 21,

            ],
            [
                "nome" => "Coutos",
                "bloco_id" => 4,
                "regiao_id" => 21,

            ],
            [
                "nome" => "Vista Alegre",
                "bloco_id" => 4,
                "regiao_id" => 21,

            ],
            [
                "nome" => "Ilha Amerela",
                "bloco_id" => 4,
                "regiao_id" => 22,

            ],
            [
                "nome" => "Rio Sena",
                "bloco_id" => 4,
                "regiao_id" => 22,

            ],
            [
                "nome" => "Lobato",
                "bloco_id" => 4,
                "regiao_id" => 23,

            ],
            [
                "nome" => "Alto do Cabrito",
                "bloco_id" => 4,
                "regiao_id" => 23,

            ],
            [
                "nome" => "Alto do Cabrito II",
                "bloco_id" => 4,
                "regiao_id" => 23,

            ],
            [
                "nome" => "Mirante de Periperi",
                "bloco_id" => 4,
                "regiao_id" => 24,

            ],
            [
                "nome" => "Paripe",
                "bloco_id" => 4,
                "regiao_id" => 25,

            ],
            [
                "nome" => "Núcleo-Ilha de Santana",
                "bloco_id" => 4,
                "regiao_id" => 25,
                
            ],
            [
                "nome" => "Ilha de São João",
                "bloco_id" => 4,
                "regiao_id" => 25,

            ],
            [
                "nome" => "Paripe II",
                "bloco_id" => 4,
                "regiao_id" => 26,

            ],
            [
                "nome" => "Paripe III",
                "bloco_id" => 4,
                "regiao_id" => 26,

            ],
            [
                "nome" => "São Tome",
                "bloco_id" => 4,
                "regiao_id" => 26,

            ],
            [
                "nome" => "Plataforma",
                "bloco_id" => 4,
                "regiao_id" => 27,

            ],
            [
                "nome" => "Itacaranha",
                "bloco_id" => 4,
                "regiao_id" => 27,

            ],
            [
                "nome" => "Volta do Tanque",
                "bloco_id" => 4,
                "regiao_id" => 27,

            ],
            [
                "nome" => "Santa Luzia",
                "bloco_id" => 4,
                "regiao_id" => 28,

            ],
            [
                "nome" => "Uruguai",
                "bloco_id" => 4,
                "regiao_id" => 29,

            ],
            [
                "nome" => "Cajazeiras X",
                "bloco_id" => 5,
                "regiao_id" => 30,

            ],
            [
                "nome" => "Boca da Mata",
                "bloco_id" => 5,
                "regiao_id" => 30,

            ],
            [
                "nome" => "Cajazeiras VIII",
                "bloco_id" => 5,
                "regiao_id" => 30,

            ],
            [
                "nome" => "Cajazeiras V",
                "bloco_id" => 5,
                "regiao_id" => 30,

            ],
            [
                "nome" => "Cajazeiras XI",
                "bloco_id" => 5,
                "regiao_id" => 30,

            ],
            [
                "nome" => "Mirante de Cajazeiras XI",
                "bloco_id" => 5,
                "regiao_id" => 30,

            ],
            [
                "nome" => "Aguas Claras",
                "bloco_id" => 5,
                "regiao_id" => 31,

            ],
            [
                "nome" => "Núcleo-Aguas Claras II",
                "bloco_id" => 5,
                "regiao_id" => 31,
                
            ],
            [
                "nome" => "Cajazeiras VI",
                "bloco_id" => 5,
                "regiao_id" => 32,

            ],
            [
                "nome" => "Caixa D'agua",
                "bloco_id" => 5,
                "regiao_id" => 32,

            ],
            [
                "nome" => "Castelo Branco",
                "bloco_id" => 5,
                "regiao_id" => 33,

            ],
            [
                "nome" => "Creche",
                "bloco_id" => 5,
                "regiao_id" => 33,

            ],
            [
                "nome" => "Dom Avelar I",
                "bloco_id" => 5,
                "regiao_id" => 33,

            ],
            [
                "nome" => "Vila Canaria",
                "bloco_id" => 5,
                "regiao_id" => 33,

            ],
            [
                "nome" => "Castelo Branco II",
                "bloco_id" => 5,
                "regiao_id" => 34,

            ],
            [
                "nome" => "Mussurunga",
                "bloco_id" => 5,
                "regiao_id" => 35,

            ],
            [
                "nome" => "Setor F",
                "bloco_id" => 5,
                "regiao_id" => 35,

            ],
            [
                "nome" => "Vila Verde",
                "bloco_id" => 5,
                "regiao_id" => 35,

            ],
            [
                "nome" => "Nova Brasilia",
                "bloco_id" => 5,
                "regiao_id" => 36,

            ],
            [
                "nome" => "Valeria",
                "bloco_id" => 5,
                "regiao_id" => 37,

            ],
            [
                "nome" => "Derba",
                "bloco_id" => 5,
                "regiao_id" => 37,

            ],
            [
                "nome" => "Palestina",
                "bloco_id" => 5,
                "regiao_id" => 37,

            ],
            [
                "nome" => "Valeria II",
                "bloco_id" => 5,
                "regiao_id" => 37,

            ],
            [
                "nome" => "Candeias",
                "bloco_id" => 6,
                "regiao_id" => 38,

            ],
            [
                "nome" => "Ferrolho",
                "bloco_id" => 6,
                "regiao_id" => 38,

            ],
            [
                "nome" => "Malemba",
                "bloco_id" => 6,
                "regiao_id" => 38,

            ],
            [
                "nome" => "Núcleo-Passe 3",
                "bloco_id" => 6,
                "regiao_id" => 38,
                
            ],
            [
                "nome" => "Socorro",
                "bloco_id" => 6,
                "regiao_id" => 38,

            ],
            [
                "nome" => "Madre de Deus",
                "bloco_id" => 6,
                "regiao_id" => 39,

            ],
            [
                "nome" => "Núcleo-Bom Jesus dos Passos",
                "bloco_id" => 6,
                "regiao_id" => 39,
                
            ],
            [
                "nome" => "Caipe",
                "bloco_id" => 6,
                "regiao_id" => 39,

            ],
            [
                "nome" => "Ponta do Cais",
                "bloco_id" => 6,
                "regiao_id" => 39,

            ],
            [
                "nome" => "Nova Candeias",
                "bloco_id" => 6,
                "regiao_id" => 40,

            ],
            [
                "nome" => "Ilha Mare",
                "bloco_id" => 6,
                "regiao_id" => 40,

            ],
            [
                "nome" => "Passe",
                "bloco_id" => 6,
                "regiao_id" => 40,

            ],
            [
                "nome" => "Passe II",
                "bloco_id" => 6,
                "regiao_id" => 40,

            ],
            [
                "nome" => "Teixeira",
                "bloco_id" => 6,
                "regiao_id" => 40,

            ],
            [
                "nome" => "São Franscico do Conde",
                "bloco_id" => 6,
                "regiao_id" => 41,

            ],
            [
                "nome" => "Núcleo-Coroado",
                "bloco_id" => 6,
                "regiao_id" => 41,
                
            ],
            [
                "nome" => "Vitória da Conquista",
                "bloco_id" => 7,
                "regiao_id" => 42,

            ],
            [
                "nome" => "Alto Maron",
                "bloco_id" => 7,
                "regiao_id" => 42,

            ],
            [
                "nome" => "Núcleo-Anage",
                "bloco_id" => 7,
                "regiao_id" => 42,
                
            ],
            [
                "nome" => "Barra Choca",
                "bloco_id" => 7,
                "regiao_id" => 42,

            ],
            [
                "nome" => "Itambe",
                "bloco_id" => 7,
                "regiao_id" => 42,

            ],
            [
                "nome" => "Núcleo-Jose Goncalves",
                "bloco_id" => 7,
                "regiao_id" => 42,
                
            ],
            [
                "nome" => "Nova Cidade",
                "bloco_id" => 7,
                "regiao_id" => 42,

            ],
            [
                "nome" => "Núcleo-Veredinha",
                "bloco_id" => 7,
                "regiao_id" => 42,
                
            ],
            [
                "nome" => "Vila America",
                "bloco_id" => 7,
                "regiao_id" => 42,

            ],
            [
                "nome" => "Bairro Brasil",
                "bloco_id" => 7,
                "regiao_id" => 43,

            ],
            [
                "nome" => "Av Brumado",
                "bloco_id" => 7,
                "regiao_id" => 43,

            ],
            [
                "nome" => "Kadija",
                "bloco_id" => 7,
                "regiao_id" => 43,

            ],
            [
                "nome" => "Patagonia",
                "bloco_id" => 7,
                "regiao_id" => 43,

            ],
            [
                "nome" => "Vila Serrana",
                "bloco_id" => 7,
                "regiao_id" => 43,

            ],
            [
                "nome" => "Brumado",
                "bloco_id" => 7,
                "regiao_id" => 44,

            ],
            [
                "nome" => "Barra da Estiva",
                "bloco_id" => 7,
                "regiao_id" => 44,

            ],
            [
                "nome" => "Núcleo-Cascavel",
                "bloco_id" => 7,
                "regiao_id" => 44,
                
            ],
            [
                "nome" => "Núcleo-Ibicoara",
                "bloco_id" => 7,
                "regiao_id" => 44,
                
            ],
            [
                "nome" => "Livramento",
                "bloco_id" => 7,
                "regiao_id" => 44,

            ],
            [
                "nome" => "Núcleo-Tanhacu",
                "bloco_id" => 7,
                "regiao_id" => 44,
                
            ],
            [
                "nome" => "Caetite",
                "bloco_id" => 7,
                "regiao_id" => 45,

            ],
            [
                "nome" => "Núcleo-Cacule",
                "bloco_id" => 7,
                "regiao_id" => 45,
                
            ],
            [
                "nome" => "Núcleo-Ibiassece",
                "bloco_id" => 7,
                "regiao_id" => 45,
                
            ],
            [
                "nome" => "Igapora",
                "bloco_id" => 7,
                "regiao_id" => 45,

            ],
            [
                "nome" => "Candido Sales",
                "bloco_id" => 7,
                "regiao_id" => 46,

            ],
            [
                "nome" => "Núcleo-Aguas Vermelhas",
                "bloco_id" => 7,
                "regiao_id" => 46,
                
            ],
            [
                "nome" => "Núcleo-Divisa Alegre",
                "bloco_id" => 7,
                "regiao_id" => 46,
                
            ],
            [
                "nome" => "Divinopolis",
                "bloco_id" => 7,
                "regiao_id" => 46,

            ],
            [
                "nome" => "Núcleo-Machado Mineiro",
                "bloco_id" => 7,
                "regiao_id" => 46,
                
            ],
            [
                "nome" => "Guanambi",
                "bloco_id" => 7,
                "regiao_id" => 47,

            ],
            [
                "nome" => "Carinhanha",
                "bloco_id" => 7,
                "regiao_id" => 47,

            ],
            [
                "nome" => "Guanambi II",
                "bloco_id" => 7,
                "regiao_id" => 47,

            ],
            [
                "nome" => "Núcleo-Iuiu",
                "bloco_id" => 7,
                "regiao_id" => 47,
                
            ],
            [
                "nome" => "Núcleo-Licinio de Almeida",
                "bloco_id" => 7,
                "regiao_id" => 47,
                
            ],
            [
                "nome" => "Núcleo-Mortugaba",
                "bloco_id" => 7,
                "regiao_id" => 47,
                
            ],
            [
                "nome" => "Palmas",
                "bloco_id" => 7,
                "regiao_id" => 47,

            ],
            [
                "nome" => "Núcleo-Urandi",
                "bloco_id" => 7,
                "regiao_id" => 47,
                
            ],
            [
                "nome" => "Ipiau",
                "bloco_id" => 7,
                "regiao_id" => 48,

            ],
            [
                "nome" => "Ibirataia",
                "bloco_id" => 7,
                "regiao_id" => 48,

            ],
            [
                "nome" => "Itagiba",
                "bloco_id" => 7,
                "regiao_id" => 48,

            ],
            [
                "nome" => "Ubatan",
                "bloco_id" => 7,
                "regiao_id" => 48,

            ],
            [
                "nome" => "Itapetinga",
                "bloco_id" => 7,
                "regiao_id" => 49,

            ],
            [
                "nome" => "Núcleo-Caatiba",
                "bloco_id" => 7,
                "regiao_id" => 49,
                
            ],
            [
                "nome" => "Itarantim",
                "bloco_id" => 7,
                "regiao_id" => 49,

            ],
            [
                "nome" => "Itororo",
                "bloco_id" => 7,
                "regiao_id" => 49,

            ],
            [
                "nome" => "Macarani",
                "bloco_id" => 7,
                "regiao_id" => 49,

            ],
            [
                "nome" => "Núcleo-Potiragua",
                "bloco_id" => 7,
                "regiao_id" => 49,
                
            ],
            [
                "nome" => "Vila Riachao",
                "bloco_id" => 7,
                "regiao_id" => 49,

            ],
            [
                "nome" => "Jaguaquara",
                "bloco_id" => 7,
                "regiao_id" => 50,

            ],
            [
                "nome" => "Entrocamento",
                "bloco_id" => 7,
                "regiao_id" => 50,

            ],
            [
                "nome" => "Núcleo-Itirucu",
                "bloco_id" => 7,
                "regiao_id" => 50,
                
            ],
            [
                "nome" => "Maracas",
                "bloco_id" => 7,
                "regiao_id" => 50,

            ],
            [
                "nome" => "Santa Ines",
                "bloco_id" => 7,
                "regiao_id" => 50,

            ],
            [
                "nome" => "Jequie",
                "bloco_id" => 7,
                "regiao_id" => 51,

            ],
            [
                "nome" => "Agua Brancas",
                "bloco_id" => 7,
                "regiao_id" => 51,

            ],
            [
                "nome" => "Núcleo-Jitauna",
                "bloco_id" => 7,
                "regiao_id" => 51,
                
            ],
            [
                "nome" => "Jequie III",
                "bloco_id" => 7,
                "regiao_id" => 51,

            ],
            [
                "nome" => "KM3",
                "bloco_id" => 7,
                "regiao_id" => 51,

            ],
            [
                "nome" => "Núcleo-Manoel Vitorino",
                "bloco_id" => 7,
                "regiao_id" => 51,
                
            ],
            [
                "nome" => "Paudeferro",
                "bloco_id" => 7,
                "regiao_id" => 51,

            ],
            [
                "nome" => "Jequie IV",
                "bloco_id" => 7,
                "regiao_id" => 52,

            ],
            [
                "nome" => "Núcleo-Itagi",
                "bloco_id" => 7,
                "regiao_id" => 52,
                
            ],
            [
                "nome" => "Paramirim",
                "bloco_id" => 7,
                "regiao_id" => 53,

            ],
            [
                "nome" => "Núcleo-Boquira",
                "bloco_id" => 7,
                "regiao_id" => 53,
                
            ],
            [
                "nome" => "Núcleo-Botipora",
                "bloco_id" => 7,
                "regiao_id" => 53,
                
            ],
            [
                "nome" => "Núcleo-Erico Cardoso",
                "bloco_id" => 7,
                "regiao_id" => 53,
                
            ],
            [
                "nome" => "Núcleo-Ibipitanga",
                "bloco_id" => 7,
                "regiao_id" => 53,
                
            ],
            [
                "nome" => "Macaubas",
                "bloco_id" => 7,
                "regiao_id" => 53,

            ],
            [
                "nome" => "Núcleo-Riodo Pires",
                "bloco_id" => 7,
                "regiao_id" => 53,
                
            ],
            [
                "nome" => "Pocoes",
                "bloco_id" => 7,
                "regiao_id" => 54,

            ],
            [
                "nome" => "Núcleo-Boa Nova",
                "bloco_id" => 7,
                "regiao_id" => 54,
                
            ],
            [
                "nome" => "Núcleo-Bom Jesus da Serra",
                "bloco_id" => 7,
                "regiao_id" => 54,
                
            ],
            [
                "nome" => "Ibicui",
                "bloco_id" => 7,
                "regiao_id" => 54,

            ],
            [
                "nome" => "Iguai",
                "bloco_id" => 7,
                "regiao_id" => 54,

            ],
            [
                "nome" => "Planalto",
                "bloco_id" => 7,
                "regiao_id" => 54,

            ],
            [
                "nome" => "Urbis VI",
                "bloco_id" => 7,
                "regiao_id" => 55,

            ],
            [
                "nome" => "Belo Campo",
                "bloco_id" => 7,
                "regiao_id" => 55,

            ],
            [
                "nome" => "Núcleo-Condeubas",
                "bloco_id" => 7,
                "regiao_id" => 55,
                
            ],
            [
                "nome" => "Núcleo-Encruzilhada",
                "bloco_id" => 7,
                "regiao_id" => 55,
                
            ],
            [
                "nome" => "Trab Especial-Limeira",
                "bloco_id" => 7,
                "regiao_id" => 55,
                
            ],
            [
                "nome" => "Núcleo-Piripa",
                "bloco_id" => 7,
                "regiao_id" => 55,
                
            ],
            [
                "nome" => "Trab Especial-Tremendal",
                "bloco_id" => 7,
                "regiao_id" => 55,
                
            ],
            [
                "nome" => "Dois Leões",
                "bloco_id" => 8,
                "regiao_id" => 56,

            ],
            [
                "nome" => "Avenida Peixe",
                "bloco_id" => 8,
                "regiao_id" => 56,

            ],
            [
                "nome" => "Lapinha",
                "bloco_id" => 8,
                "regiao_id" => 56,

            ],
            [
                "nome" => "Paumiudo",
                "bloco_id" => 8,
                "regiao_id" => 56,

            ],
            [
                "nome" => "Retiro",
                "bloco_id" => 8,
                "regiao_id" => 56,

            ],
            [
                "nome" => "Aquidaba",
                "bloco_id" => 8,
                "regiao_id" => 57,

            ],
            [
                "nome" => "Brotas",
                "bloco_id" => 8,
                "regiao_id" => 58,

            ],
            [
                "nome" => "Cine Centro",
                "bloco_id" => 8,
                "regiao_id" => 59,

            ],
            [
                "nome" => "Tijolo",
                "bloco_id" => 8,
                "regiao_id" => 59,

            ],
            [
                "nome" => "Cosme de Farias",
                "bloco_id" => 8,
                "regiao_id" => 60,

            ],
            [
                "nome" => "Eng Velho de Brotas",
                "bloco_id" => 8,
                "regiao_id" => 61,

            ],
            [
                "nome" => "Federação",
                "bloco_id" => 8,
                "regiao_id" => 62,

            ],
            [
                "nome" => "Alto das Pombas",
                "bloco_id" => 8,
                "regiao_id" => 62,

            ],
            [
                "nome" => "Eng Velho de Federação",
                "bloco_id" => 8,
                "regiao_id" => 62,

            ],
            [
                "nome" => "Liberdade",
                "bloco_id" => 8,
                "regiao_id" => 63,

            ],
            [
                "nome" => "Meireles",
                "bloco_id" => 8,
                "regiao_id" => 63,

            ],
            [
                "nome" => "Matatu de Brotas",
                "bloco_id" => 8,
                "regiao_id" => 64,

            ],
            [
                "nome" => "Luis Anselmo",
                "bloco_id" => 8,
                "regiao_id" => 64,

            ],
            [
                "nome" => "Pau da Lima",
                "bloco_id" => 8,
                "regiao_id" => 65,

            ],
            [
                "nome" => "Jardim Cajazeiras",
                "bloco_id" => 8,
                "regiao_id" => 65,

            ],
            [
                "nome" => "Vale da Muriçoca",
                "bloco_id" => 8,
                "regiao_id" => 66,

            ],
            [
                "nome" => "Alto da Ondina",
                "bloco_id" => 8,
                "regiao_id" => 66,

            ],
            [
                "nome" => "Vale do Matatu",
                "bloco_id" => 8,
                "regiao_id" => 67,

            ],
            [
                "nome" => "Vasco da Gama",
                "bloco_id" => 8,
                "regiao_id" => 68,

            ],
            [
                "nome" => "Fazenda Grande",
                "bloco_id" => 9,
                "regiao_id" => 69,

            ],
            [
                "nome" => "Jaqueira",
                "bloco_id" => 9,
                "regiao_id" => 69,

            ],
            [
                "nome" => "Campinas de Piraja",
                "bloco_id" => 9,
                "regiao_id" => 70,

            ],
            [
                "nome" => "Calabetao",
                "bloco_id" => 9,
                "regiao_id" => 70,

            ],
            [
                "nome" => "Largo do Tanque",
                "bloco_id" => 9,
                "regiao_id" => 71,

            ],
            [
                "nome" => "Piraja do Lobato",
                "bloco_id" => 9,
                "regiao_id" => 72,

            ],
            [
                "nome" => "Piraja",
                "bloco_id" => 9,
                "regiao_id" => 72,

            ],
            [
                "nome" => "San Martin",
                "bloco_id" => 9,
                "regiao_id" => 73,

            ],
            [
                "nome" => "IAPI",
                "bloco_id" => 9,
                "regiao_id" => 73,

            ],
            [
                "nome" => "Santa Monica",
                "bloco_id" => 9,
                "regiao_id" => 73,

            ],
            [
                "nome" => "São Caetano",
                "bloco_id" => 9,
                "regiao_id" => 74,

            ],
            [
                "nome" => "Boa Vista",
                "bloco_id" => 9,
                "regiao_id" => 74,

            ],
            [
                "nome" => "Boa Vista II",
                "bloco_id" => 9,
                "regiao_id" => 74,

            ],
            [
                "nome" => "Capilinha",
                "bloco_id" => 9,
                "regiao_id" => 74,

            ],
            [
                "nome" => "Feira de Santana",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Asa Branca",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "FeiraX",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "George Americo",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Núcleo-Lamarao",
                "bloco_id" => 10,
                "regiao_id" => 75,
                
            ],
            [
                "nome" => "Queimadinha",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Rua Nova",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Santa Barbara",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Santo Antonio",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Sobradinho",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Sobradinho II",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Tome de Souza",
                "bloco_id" => 10,
                "regiao_id" => 75,

            ],
            [
                "nome" => "Amelia Rodrigues",
                "bloco_id" => 10,
                "regiao_id" => 76,

            ],
            [
                "nome" => "Trab Especial-Aliança",
                "bloco_id" => 10,
                "regiao_id" => 76,
                
            ],
            [
                "nome" => "Núcleo-Jacu",
                "bloco_id" => 10,
                "regiao_id" => 76,
                
            ],
            [
                "nome" => "Núcleo-São Bento",
                "bloco_id" => 10,
                "regiao_id" => 76,
                
            ],
            [
                "nome" => "Terra Nova",
                "bloco_id" => 10,
                "regiao_id" => 76,

            ],
            [
                "nome" => "Campo Limpo",
                "bloco_id" => 10,
                "regiao_id" => 77,

            ],
            [
                "nome" => "Boqueirão",
                "bloco_id" => 10,
                "regiao_id" => 77,

            ],
            [
                "nome" => "Jardim Cruzeiro",
                "bloco_id" => 10,
                "regiao_id" => 77,

            ],
            [
                "nome" => "Núcleo-Maria Quiteria",
                "bloco_id" => 10,
                "regiao_id" => 77,
                
            ],
            [
                "nome" => "Núcleo-Novo Horizonte",
                "bloco_id" => 10,
                "regiao_id" => 77,
                
            ],
            [
                "nome" => "Núcleo-São Jose",
                "bloco_id" => 10,
                "regiao_id" => 77,
                
            ],
            [
                "nome" => "Parque Ipe",
                "bloco_id" => 10,
                "regiao_id" => 77,

            ],
            [
                "nome" => "Conceição do Coite",
                "bloco_id" => 10,
                "regiao_id" => 78,

            ],
            [
                "nome" => "Núcleo-Retirolandia",
                "bloco_id" => 10,
                "regiao_id" => 78,
                
            ],
            [
                "nome" => "Riachao do Jacuipe",
                "bloco_id" => 10,
                "regiao_id" => 78,

            ],
            [
                "nome" => "Santa Luz",
                "bloco_id" => 10,
                "regiao_id" => 78,

            ],
            [
                "nome" => "Valente",
                "bloco_id" => 10,
                "regiao_id" => 78,

            ],
            [
                "nome" => "Conceição do Jacuípe",
                "bloco_id" => 10,
                "regiao_id" => 79,

            ],
            [
                "nome" => "Coração de Maria",
                "bloco_id" => 10,
                "regiao_id" => 79,

            ],
            [
                "nome" => "Núcleo-Lustosa",
                "bloco_id" => 10,
                "regiao_id" => 79,
                
            ],
            [
                "nome" => "Ipira",
                "bloco_id" => 10,
                "regiao_id" => 80,

            ],
            [
                "nome" => "Anguera",
                "bloco_id" => 10,
                "regiao_id" => 80,

            ],
            [
                "nome" => "Baixa Grande",
                "bloco_id" => 10,
                "regiao_id" => 80,

            ],
            [
                "nome" => "Bravo",
                "bloco_id" => 10,
                "regiao_id" => 80,

            ],
            [
                "nome" => "Serra Preta",
                "bloco_id" => 10,
                "regiao_id" => 80,

            ],
            [
                "nome" => "Mundo Novo",
                "bloco_id" => 10,
                "regiao_id" => 80,

            ],
            [
                "nome" => "Núcleo-Romana",
                "bloco_id" => 10,
                "regiao_id" => 80,
                
            ],
            [
                "nome" => "Irece",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Núcleo-Angical",
                "bloco_id" => 10,
                "regiao_id" => 81,
                
            ],
            [
                "nome" => "Núcleo-Artur Alves",
                "bloco_id" => 10,
                "regiao_id" => 81,
                
            ],
            [
                "nome" => "Núcleo-Barra do Mendes",
                "bloco_id" => 10,
                "regiao_id" => 81,
                
            ],
            [
                "nome" => "Barro Alto",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Cafarnaum",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Canarana",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Central",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Trab Especial-Ibitita",
                "bloco_id" => 10,
                "regiao_id" => 81,
                
            ],
            [
                "nome" => "Irece II",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Joao Dourado",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Lapao",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Morro do Chapeu",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Núcleo-Palmeiras",
                "bloco_id" => 10,
                "regiao_id" => 81,
                
            ],
            [
                "nome" => "Piata",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Presidente Dutra II",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Salobro",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Seabra",
                "bloco_id" => 10,
                "regiao_id" => 81,

            ],
            [
                "nome" => "Núcleo-Uibai",
                "bloco_id" => 10,
                "regiao_id" => 81,
                
            ],
            [
                "nome" => "Jacobina",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Capim Grosso",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Núcleo-Gaviao",
                "bloco_id" => 10,
                "regiao_id" => 82,
                
            ],
            [
                "nome" => "Jacobina IV",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Miguel Calmon",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Piritiba",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Saude",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Serrolandia",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Núcleo-Umburanas",
                "bloco_id" => 10,
                "regiao_id" => 82,
                
            ],
            [
                "nome" => "Núcleo-Varzea do Poco",
                "bloco_id" => 10,
                "regiao_id" => 82,
                
            ],
            [
                "nome" => "Varzea Nova",
                "bloco_id" => 10,
                "regiao_id" => 82,

            ],
            [
                "nome" => "Presidente Dutra",
                "bloco_id" => 10,
                "regiao_id" => 83,

            ],
            [
                "nome" => "Aviario",
                "bloco_id" => 10,
                "regiao_id" => 83,

            ],
            [
                "nome" => "Humildes",
                "bloco_id" => 10,
                "regiao_id" => 83,

            ],
            [
                "nome" => "Limoeiro",
                "bloco_id" => 10,
                "regiao_id" => 83,

            ],
            [
                "nome" => "Santo Estevão",
                "bloco_id" => 10,
                "regiao_id" => 84,

            ],
            [
                "nome" => "Pilao",
                "bloco_id" => 10,
                "regiao_id" => 84,

            ],
            [
                "nome" => "Rafael Jambeiro",
                "bloco_id" => 10,
                "regiao_id" => 84,

            ],
            [
                "nome" => "São Gonçalos dos Campos",
                "bloco_id" => 10,
                "regiao_id" => 85,

            ],
            [
                "nome" => "Conceicao de Feira",
                "bloco_id" => 10,
                "regiao_id" => 85,

            ],
            [
                "nome" => "Xique Xique",
                "bloco_id" => 10,
                "regiao_id" => 86,

            ],
            [
                "nome" => "Barrado Rio Grande",
                "bloco_id" => 10,
                "regiao_id" => 86,

            ],
            [
                "nome" => "Feirade Santana II",
                "bloco_id" => 11,
                "regiao_id" => 87,

            ],
            [
                "nome" => "Irara",
                "bloco_id" => 11,
                "regiao_id" => 87,

            ],
            [
                "nome" => "Mangabeira",
                "bloco_id" => 11,
                "regiao_id" => 87,

            ],
            [
                "nome" => "Núcleo-Ouricanga",
                "bloco_id" => 11,
                "regiao_id" => 87,
                
            ],
            [
                "nome" => "Santanopolis",
                "bloco_id" => 11,
                "regiao_id" => 87,

            ],
            [
                "nome" => "Viveiros",
                "bloco_id" => 11,
                "regiao_id" => 87,

            ],
            [
                "nome" => "Cachoeira",
                "bloco_id" => 11,
                "regiao_id" => 88,

            ],
            [
                "nome" => "Capoeirucu",
                "bloco_id" => 11,
                "regiao_id" => 88,

            ],
            [
                "nome" => "Núcleo-Coqueiro",
                "bloco_id" => 11,
                "regiao_id" => 88,
                
            ],
            [
                "nome" => "Maragogipe",
                "bloco_id" => 11,
                "regiao_id" => 88,

            ],
            [
                "nome" => "Núcleo-Tabuleiro",
                "bloco_id" => 11,
                "regiao_id" => 88,
                
            ],
            [
                "nome" => "Cansanção",
                "bloco_id" => 11,
                "regiao_id" => 89,

            ],
            [
                "nome" => "Núcleo-Nordestina",
                "bloco_id" => 11,
                "regiao_id" => 89,
                
            ],
            [
                "nome" => "Núcleo-Queimadas",
                "bloco_id" => 11,
                "regiao_id" => 89,
                
            ],
            [
                "nome" => "Núcleo-Romulo Campos",
                "bloco_id" => 11,
                "regiao_id" => 89,
                
            ],
            [
                "nome" => "Euclides da Cunha",
                "bloco_id" => 11,
                "regiao_id" => 90,

            ],
            [
                "nome" => "Monte Santo",
                "bloco_id" => 11,
                "regiao_id" => 90,

            ],
            [
                "nome" => "Núcleo-Quinjingue",
                "bloco_id" => 11,
                "regiao_id" => 90,
                
            ],
            [
                "nome" => "Tucano",
                "bloco_id" => 11,
                "regiao_id" => 90,

            ],
            [
                "nome" => "Núcleo-Uaua",
                "bloco_id" => 11,
                "regiao_id" => 90,
                
            ],
            [
                "nome" => "Itaberaba",
                "bloco_id" => 11,
                "regiao_id" => 91,

            ],
            [
                "nome" => "Núcleo-Boa Vista do Tupim",
                "bloco_id" => 11,
                "regiao_id" => 91,
                
            ],
            [
                "nome" => "Iacu",
                "bloco_id" => 11,
                "regiao_id" => 91,

            ],
            [
                "nome" => "Núcleo-Lajedinho",
                "bloco_id" => 11,
                "regiao_id" => 91,
                
            ],
            [
                "nome" => "Rui Barrosa",
                "bloco_id" => 11,
                "regiao_id" => 91,

            ],
            [
                "nome" => "Utinga",
                "bloco_id" => 11,
                "regiao_id" => 91,

            ],
            [
                "nome" => "Núcleo-Wagner",
                "bloco_id" => 11,
                "regiao_id" => 91,
                
            ],
            [
                "nome" => "Muritiba",
                "bloco_id" => 11,
                "regiao_id" => 92,

            ],
            [
                "nome" => "Cabeceiras",
                "bloco_id" => 11,
                "regiao_id" => 92,

            ],
            [
                "nome" => "Gov Mangabeira",
                "bloco_id" => 11,
                "regiao_id" => 92,

            ],
            [
                "nome" => "Serrinha",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Agua Fria",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Araci",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Barrocas",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Biritinga",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Núcleo-Candeal",
                "bloco_id" => 11,
                "regiao_id" => 93,
                
            ],
            [
                "nome" => "Cidade Nova",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Núcleo-Mombaca",
                "bloco_id" => 11,
                "regiao_id" => 93,
                
            ],
            [
                "nome" => "Núcleo-Quilombola",
                "bloco_id" => 11,
                "regiao_id" => 93,
                
            ],
            [
                "nome" => "Núcleo-Quinjin",
                "bloco_id" => 11,
                "regiao_id" => 93,
                
            ],
            [
                "nome" => "Núcleo-Teabaia",
                "bloco_id" => 11,
                "regiao_id" => 93,
                
            ],
            [
                "nome" => "Teofilandia",
                "bloco_id" => 11,
                "regiao_id" => 93,

            ],
            [
                "nome" => "Tomba",
                "bloco_id" => 11,
                "regiao_id" => 94,

            ],
            [
                "nome" => "Feira VII",
                "bloco_id" => 11,
                "regiao_id" => 94,

            ],
            [
                "nome" => "Sergio Carneiro",
                "bloco_id" => 11,
                "regiao_id" => 94,

            ],
            [
                "nome" => "Tomba I",
                "bloco_id" => 11,
                "regiao_id" => 94,

            ],
            [
                "nome" => "Itabuna",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Núcleo-Aurelino Leal",
                "bloco_id" => 12,
                "regiao_id" => 95,
                
            ],
            [
                "nome" => "Buerarema",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Coaraci",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Itajuipe",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Jorge Amado",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Nova Ferradas",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "São Jose da Vitoria",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Ubaitaba",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Urucuca",
                "bloco_id" => 12,
                "regiao_id" => 95,

            ],
            [
                "nome" => "Itabuna II",
                "bloco_id" => 12,
                "regiao_id" => 96,

            ],
            [
                "nome" => "Itabuna IV",
                "bloco_id" => 12,
                "regiao_id" => 96,

            ],
            [
                "nome" => "Núcleo-Santa Ines II",
                "bloco_id" => 12,
                "regiao_id" => 96,
                
            ],
            [
                "nome" => "São Pedro",
                "bloco_id" => 12,
                "regiao_id" => 96,

            ],
            [
                "nome" => "Itabuna III",
                "bloco_id" => 12,
                "regiao_id" => 97,

            ],
            [
                "nome" => "Camacan",
                "bloco_id" => 12,
                "regiao_id" => 97,

            ],
            [
                "nome" => "Núcleo-Itape",
                "bloco_id" => 12,
                "regiao_id" => 97,
                
            ],
            [
                "nome" => "Núcleo-Maria Pinheiro",
                "bloco_id" => 12,
                "regiao_id" => 97,
                
            ],
            [
                "nome" => "Ilheus",
                "bloco_id" => 12,
                "regiao_id" => 98,

            ],
            [
                "nome" => "Núcleo-Bco Vitoria",
                "bloco_id" => 12,
                "regiao_id" => 98,
                
            ],
            [
                "nome" => "Itacare",
                "bloco_id" => 12,
                "regiao_id" => 98,

            ],
            [
                "nome" => "Nelson Costa",
                "bloco_id" => 12,
                "regiao_id" => 98,

            ],
            [
                "nome" => "Núcleo-Salobrinho",
                "bloco_id" => 12,
                "regiao_id" => 98,
                
            ],
            [
                "nome" => "Vitoria",
                "bloco_id" => 12,
                "regiao_id" => 98,

            ],
            [
                "nome" => "Canavieras",
                "bloco_id" => 12,
                "regiao_id" => 99,

            ],
            [
                "nome" => "Núcleo-Paraiso",
                "bloco_id" => 12,
                "regiao_id" => 99,
                
            ],
            [
                "nome" => "Una",
                "bloco_id" => 12,
                "regiao_id" => 99,

            ],
            [
                "nome" => "Lomanto",
                "bloco_id" => 12,
                "regiao_id" => 100,

            ],
            [
                "nome" => "Núcleo-Barro Preto",
                "bloco_id" => 12,
                "regiao_id" => 100,
                
            ],
            [
                "nome" => "Ibicarai",
                "bloco_id" => 12,
                "regiao_id" => 100,

            ],
            [
                "nome" => "Santa Cruz II",
                "bloco_id" => 12,
                "regiao_id" => 100,

            ],
            [
                "nome" => "Malhado",
                "bloco_id" => 12,
                "regiao_id" => 101,

            ],
            [
                "nome" => "Iguape",
                "bloco_id" => 12,
                "regiao_id" => 101,

            ],
            [
                "nome" => "Núcleo-Palmares",
                "bloco_id" => 12,
                "regiao_id" => 101,
                
            ],
            [
                "nome" => "Teotonio Viela",
                "bloco_id" => 12,
                "regiao_id" => 101,

            ],
            [
                "nome" => "Itapuã",
                "bloco_id" => 13,
                "regiao_id" => 102,

            ],
            [
                "nome" => "Avenida Aliomar",
                "bloco_id" => 13,
                "regiao_id" => 103,

            ],
            [
                "nome" => "Bairro da Paz",
                "bloco_id" => 13,
                "regiao_id" => 104,

            ],
            [
                "nome" => "Mangueira",
                "bloco_id" => 13,
                "regiao_id" => 104,

            ],
            [
                "nome" => "Paralela",
                "bloco_id" => 13,
                "regiao_id" => 104,

            ],
            [
                "nome" => "Boca do Rio",
                "bloco_id" => 13,
                "regiao_id" => 105,

            ],
            [
                "nome" => "Boca do Rio I",
                "bloco_id" => 13,
                "regiao_id" => 105,

            ],
            [
                "nome" => "Bocado Rio II",
                "bloco_id" => 13,
                "regiao_id" => 106,

            ],
            [
                "nome" => "Imbui",
                "bloco_id" => 13,
                "regiao_id" => 107,

            ],
            [
                "nome" => "São Cristovão",
                "bloco_id" => 13,
                "regiao_id" => 108,

            ],
            [
                "nome" => "Pq São Cristovao",
                "bloco_id" => 13,
                "regiao_id" => 108,

            ],
            [
                "nome" => "Juazeiro",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Núcleo-Alto do Alinça",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "Núcleo-Caraibas",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "Núcleo-Carnaiba",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "Casa Nova",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Castelo Branco Juzeiro II",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Núcleo-Curuça",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "Núcleo-José Rodrigues",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "Juazeiro VIII",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Kide",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Trab Especial-Maniçoba",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "Piranga",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Núcleo-Santana",
                "bloco_id" => 14,
                "regiao_id" => 109,
                
            ],
            [
                "nome" => "São Franscico do Juazeiro",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Sento Sé",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Sobradinho III",
                "bloco_id" => 14,
                "regiao_id" => 109,

            ],
            [
                "nome" => "Bonfim",
                "bloco_id" => 14,
                "regiao_id" => 110,

            ],
            [
                "nome" => "Núcleo-Andorinha",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Núcleo-Antonio Goncalves",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Campo Formoso",
                "bloco_id" => 14,
                "regiao_id" => 110,

            ],
            [
                "nome" => "Núcleo-Filadelfia",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Trab Especial-Horizonte Novo",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Núcleo-Igara",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Núcleo-Jaguarari",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Pindobacu",
                "bloco_id" => 14,
                "regiao_id" => 110,

            ],
            [
                "nome" => "Núcleo-Ponto Novo",
                "bloco_id" => 14,
                "regiao_id" => 110,
                
            ],
            [
                "nome" => "Paulo Afonso",
                "bloco_id" => 14,
                "regiao_id" => 111,

            ],
            [
                "nome" => "Núcleo-Gloria",
                "bloco_id" => 14,
                "regiao_id" => 111,
                
            ],
            [
                "nome" => "Jardim Bahia",
                "bloco_id" => 14,
                "regiao_id" => 111,

            ],
            [
                "nome" => "Jeremoabo",
                "bloco_id" => 14,
                "regiao_id" => 111,

            ],
            [
                "nome" => "Núcleo-Riacho",
                "bloco_id" => 14,
                "regiao_id" => 111,
                
            ],
            [
                "nome" => "TrabEspecial-Rodelas",
                "bloco_id" => 14,
                "regiao_id" => 111,
                
            ],
            [
                "nome" => "Tancredo Neves IV",
                "bloco_id" => 14,
                "regiao_id" => 111,

            ],
            [
                "nome" => "Remanso",
                "bloco_id" => 14,
                "regiao_id" => 112,

            ],
            [
                "nome" => "Núcleo-Campo Alegre",
                "bloco_id" => 14,
                "regiao_id" => 112,
                
            ],
            [
                "nome" => "Pilao Arcado",
                "bloco_id" => 14,
                "regiao_id" => 112,

            ],
            [
                "nome" => "Pernambues",
                "bloco_id" => 15,
                "regiao_id" => 113,

            ],
            [
                "nome" => "Núcleo-Cabula",
                "bloco_id" => 15,
                "regiao_id" => 113,
                
            ],
            [
                "nome" => "Saramandaia",
                "bloco_id" => 15,
                "regiao_id" => 113,

            ],
            [
                "nome" => "São Gonçalo do Retiro",
                "bloco_id" => 15,
                "regiao_id" => 113,

            ],
            [
                "nome" => "Barra",
                "bloco_id" => 15,
                "regiao_id" => 114,

            ],
            [
                "nome" => "Calabar",
                "bloco_id" => 15,
                "regiao_id" => 114,

            ],
            [
                "nome" => "Itaparica",
                "bloco_id" => 15,
                "regiao_id" => 115,

            ],
            [
                "nome" => "Gameleira",
                "bloco_id" => 15,
                "regiao_id" => 115,

            ],
            [
                "nome" => "Núcleo-Misericordia",
                "bloco_id" => 15,
                "regiao_id" => 115,
                
            ],
            [
                "nome" => "Mar Grande",
                "bloco_id" => 15,
                "regiao_id" => 116,

            ],
            [
                "nome" => "Aratuba",
                "bloco_id" => 15,
                "regiao_id" => 116,

            ],
            [
                "nome" => "Núcleo-Baiacu",
                "bloco_id" => 15,
                "regiao_id" => 116,
                
            ],
            [
                "nome" => "Barra do Gil",
                "bloco_id" => 15,
                "regiao_id" => 116,

            ],
            [
                "nome" => "Barra do Pote",
                "bloco_id" => 15,
                "regiao_id" => 116,

            ],
            [
                "nome" => "Gamboa II",
                "bloco_id" => 15,
                "regiao_id" => 116,

            ],
            [
                "nome" => "Núcleo-Jiribatuba",
                "bloco_id" => 15,
                "regiao_id" => 116,
                
            ],
            [
                "nome" => "Nordeste",
                "bloco_id" => 15,
                "regiao_id" => 117,

            ],
            [
                "nome" => "Pituba",
                "bloco_id" => 15,
                "regiao_id" => 118,

            ],
            [
                "nome" => "Rio Vermelho",
                "bloco_id" => 15,
                "regiao_id" => 118,

            ],
            [
                "nome" => "Santa Cruz",
                "bloco_id" => 15,
                "regiao_id" => 119,

            ],
            [
                "nome" => "Alto da Santa Cruz",
                "bloco_id" => 15,
                "regiao_id" => 119,

            ],
            [
                "nome" => "São Rafael",
                "bloco_id" => 15,
                "regiao_id" => 120,

            ],
            [
                "nome" => "Canabrava",
                "bloco_id" => 15,
                "regiao_id" => 120,

            ],
            [
                "nome" => "Sete de Abril",
                "bloco_id" => 15,
                "regiao_id" => 121,

            ],
            [
                "nome" => "Maria Lucia",
                "bloco_id" => 15,
                "regiao_id" => 121,

            ],
            [
                "nome" => "Nova Esperança",
                "bloco_id" => 15,
                "regiao_id" => 121,

            ],
            [
                "nome" => "Vale das Pedrinhas",
                "bloco_id" => 15,
                "regiao_id" => 122,

            ],
            [
                "nome" => "Santo Antônio de Jesus",
                "bloco_id" => 16,
                "regiao_id" => 123,

            ],
            [
                "nome" => "Andai",
                "bloco_id" => 16,
                "regiao_id" => 123,

            ],
            [
                "nome" => "Núcleo-Areia Grosso",
                "bloco_id" => 16,
                "regiao_id" => 123,
                
            ],
            [
                "nome" => "Núcleo-Dom Macedo Costa",
                "bloco_id" => 16,
                "regiao_id" => 123,
                
            ],
            [
                "nome" => "Núcleo-Jequirica",
                "bloco_id" => 16,
                "regiao_id" => 123,
                
            ],
            [
                "nome" => "Tancredo Neves III",
                "bloco_id" => 16,
                "regiao_id" => 123,

            ],
            [
                "nome" => "Ubaira",
                "bloco_id" => 16,
                "regiao_id" => 123,

            ],
            [
                "nome" => "Amargosa",
                "bloco_id" => 16,
                "regiao_id" => 124,

            ],
            [
                "nome" => "Núcleo-Brejoes",
                "bloco_id" => 16,
                "regiao_id" => 124,
                
            ],
            [
                "nome" => "Catiara",
                "bloco_id" => 16,
                "regiao_id" => 124,

            ],
            [
                "nome" => "Núcleo-Elisio Medrado",
                "bloco_id" => 16,
                "regiao_id" => 124,
                
            ],
            [
                "nome" => "Núcleo-KM 100",
                "bloco_id" => 16,
                "regiao_id" => 124,
                
            ],
            [
                "nome" => "Milagres",
                "bloco_id" => 16,
                "regiao_id" => 124,

            ],
            [
                "nome" => "Mutuipe",
                "bloco_id" => 16,
                "regiao_id" => 124,

            ],
            [
                "nome" => "Castro Alves",
                "bloco_id" => 16,
                "regiao_id" => 125,

            ],
            [
                "nome" => "Itatim",
                "bloco_id" => 16,
                "regiao_id" => 125,

            ],
            [
                "nome" => "Núcleo-Santa Terezinha II",
                "bloco_id" => 16,
                "regiao_id" => 125,
                
            ],
            [
                "nome" => "Cruz das Almas",
                "bloco_id" => 16,
                "regiao_id" => 126,

            ],
            [
                "nome" => "Núcleo-Chapada",
                "bloco_id" => 16,
                "regiao_id" => 126,
                
            ],
            [
                "nome" => "Núcleo-Conceicao de Almeida",
                "bloco_id" => 16,
                "regiao_id" => 126,
                
            ],
            [
                "nome" => "Cruz das Almas II",
                "bloco_id" => 16,
                "regiao_id" => 126,

            ],
            [
                "nome" => "Pau Ferro",
                "bloco_id" => 16,
                "regiao_id" => 126,

            ],
            [
                "nome" => "São Jose do Itapora",
                "bloco_id" => 16,
                "regiao_id" => 126,

            ],
            [
                "nome" => "Sapeacu",
                "bloco_id" => 16,
                "regiao_id" => 126,

            ],
            [
                "nome" => "Gandu",
                "bloco_id" => 16,
                "regiao_id" => 127,

            ],
            [
                "nome" => "Núcleo-Apuarema",
                "bloco_id" => 16,
                "regiao_id" => 127,
                
            ],
            [
                "nome" => "Itamari",
                "bloco_id" => 16,
                "regiao_id" => 127,

            ],
            [
                "nome" => "Núcleo-Pirai do Norte",
                "bloco_id" => 16,
                "regiao_id" => 127,
                
            ],
            [
                "nome" => "Núcleo-Teolandia",
                "bloco_id" => 16,
                "regiao_id" => 127,
                
            ],
            [
                "nome" => "W Guimaraes",
                "bloco_id" => 16,
                "regiao_id" => 127,

            ],
            [
                "nome" => "Nazare",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "Aratuipe",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "Encarnação",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "Núcleo-Enseada de Salinas",
                "bloco_id" => 16,
                "regiao_id" => 128,
                
            ],
            [
                "nome" => "Núcleo-Junco",
                "bloco_id" => 16,
                "regiao_id" => 128,
                
            ],
            [
                "nome" => "Núcleo-Muniz Ferreira",
                "bloco_id" => 16,
                "regiao_id" => 128,
                
            ],
            [
                "nome" => "Nazare II",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "Salinas",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "São Bernado",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "São Roque",
                "bloco_id" => 16,
                "regiao_id" => 128,

            ],
            [
                "nome" => "São Benedito",
                "bloco_id" => 16,
                "regiao_id" => 129,

            ],
            [
                "nome" => "Varzedo",
                "bloco_id" => 16,
                "regiao_id" => 129,

            ],
            [
                "nome" => "Núcleo-Laje",
                "bloco_id" => 16,
                "regiao_id" => 129,
                
            ],
            [
                "nome" => "Valença",
                "bloco_id" => 16,
                "regiao_id" => 130,

            ],
            [
                "nome" => "Núcleo-Gamboa",
                "bloco_id" => 16,
                "regiao_id" => 130,
                
            ],
            [
                "nome" => "Morro de São Paulo",
                "bloco_id" => 16,
                "regiao_id" => 130,

            ],
            [
                "nome" => "Pitanga",
                "bloco_id" => 16,
                "regiao_id" => 130,

            ],
            [
                "nome" => "Taperoa",
                "bloco_id" => 16,
                "regiao_id" => 130,

            ],
            [
                "nome" => "Valença II Bolivia",
                "bloco_id" => 16,
                "regiao_id" => 131,

            ],
            [
                "nome" => "Camamu",
                "bloco_id" => 16,
                "regiao_id" => 131,

            ],
            [
                "nome" => "Itubera",
                "bloco_id" => 16,
                "regiao_id" => 131,

            ],
            [
                "nome" => "Núcleo-Nilo Pecanha",
                "bloco_id" => 16,
                "regiao_id" => 131,
                
            ],
            [
                "nome" => "Simões Filho",
                "bloco_id" => 17,
                "regiao_id" => 132,

            ],
            [
                "nome" => "KM 25",
                "bloco_id" => 17,
                "regiao_id" => 132,

            ],
            [
                "nome" => "Mapele",
                "bloco_id" => 17,
                "regiao_id" => 132,

            ],
            [
                "nome" => "Núcleo-Renatao",
                "bloco_id" => 17,
                "regiao_id" => 132,
                
            ],
            [
                "nome" => "Simoes Filho II",
                "bloco_id" => 17,
                "regiao_id" => 132,

            ],
            [
                "nome" => "AreiaBrancaII",
                "bloco_id" => 17,
                "regiao_id" => 133,

            ],
            [
                "nome" => "Capelao",
                "bloco_id" => 17,
                "regiao_id" => 133,

            ],
            [
                "nome" => "Ceasa",
                "bloco_id" => 17,
                "regiao_id" => 133,

            ],
            [
                "nome" => "CIA",
                "bloco_id" => 17,
                "regiao_id" => 134,

            ],
            [
                "nome" => "Cia I",
                "bloco_id" => 17,
                "regiao_id" => 134,

            ],
            [
                "nome" => "Pitanguinha",
                "bloco_id" => 17,
                "regiao_id" => 134,

            ],
            [
                "nome" => "Pitanguinha II",
                "bloco_id" => 17,
                "regiao_id" => 134,

            ],
            [
                "nome" => "Santo Amaro",
                "bloco_id" => 17,
                "regiao_id" => 135,

            ],
            [
                "nome" => "Núcleo-Entrada da Pedra",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Núcleo-Lama Branca",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Núcleo-Nova Pitanga",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Núcleo-Opalma",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Pilar",
                "bloco_id" => 17,
                "regiao_id" => 135,

            ],
            [
                "nome" => "Núcleo-Retiro de Santo Amaro",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Santo Amaro II",
                "bloco_id" => 17,
                "regiao_id" => 135,

            ],
            [
                "nome" => "Núcleo-São Francisco",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Núcleo-Sitio Camacari",
                "bloco_id" => 17,
                "regiao_id" => 135,
                
            ],
            [
                "nome" => "Saubara",
                "bloco_id" => 17,
                "regiao_id" => 136,

            ],
            [
                "nome" => "Acupe",
                "bloco_id" => 17,
                "regiao_id" => 136,

            ],
            [
                "nome" => "Bom Jesus",
                "bloco_id" => 17,
                "regiao_id" => 136,

            ],
            [
                "nome" => "Cabucu",
                "bloco_id" => 17,
                "regiao_id" => 136,

            ],
            [
                "nome" => "Tancredo Neves",
                "bloco_id" => 18,
                "regiao_id" => 137,

            ],
            [
                "nome" => "Arvoredo",
                "bloco_id" => 18,
                "regiao_id" => 137,

            ],
            [
                "nome" => "Narandiba",
                "bloco_id" => 18,
                "regiao_id" => 137,

            ],
            [
                "nome" => "Saboeiro",
                "bloco_id" => 18,
                "regiao_id" => 137,

            ],
            [
                "nome" => "Saboeiro II",
                "bloco_id" => 18,
                "regiao_id" => 137,

            ],
            [
                "nome" => "Engomadeira",
                "bloco_id" => 18,
                "regiao_id" => 138,

            ],
            [
                "nome" => "Baixinha",
                "bloco_id" => 18,
                "regiao_id" => 138,

            ],
            [
                "nome" => "Estrada das Barreiras",
                "bloco_id" => 18,
                "regiao_id" => 139,

            ],
            [
                "nome" => "Sussuarana",
                "bloco_id" => 18,
                "regiao_id" => 140,

            ],
            [
                "nome" => "Areal",
                "bloco_id" => 18,
                "regiao_id" => 140,

            ],
            [
                "nome" => "Mata Escura",
                "bloco_id" => 18,
                "regiao_id" => 140,

            ],
            [
                "nome" => "Novo Horizonte de Sussuarana",
                "bloco_id" => 18,
                "regiao_id" => 140,

            ],
            [
                "nome" => "Santo Inacio",
                "bloco_id" => 18,
                "regiao_id" => 140,

            ],
            [
                "nome" => "Sussuarana Nova",
                "bloco_id" => 18,
                "regiao_id" => 140,

            ],
            [
                "nome" => "Teixeira de Freitas",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Alcobaca",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Itabatam",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Itanhem",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Núcleo-Juerana",
                "bloco_id" => 19,
                "regiao_id" => 141,
                
            ],
            [
                "nome" => "Medeiros Neto",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Mucuri",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Posto da Mata",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Núcleo-Rancho Alegre",
                "bloco_id" => 19,
                "regiao_id" => 141,
                
            ],
            [
                "nome" => "São Lourenco",
                "bloco_id" => 19,
                "regiao_id" => 141,

            ],
            [
                "nome" => "Baianao II",
                "bloco_id" => 19,
                "regiao_id" => 142,

            ],
            [
                "nome" => "Coroa Vermelha",
                "bloco_id" => 19,
                "regiao_id" => 142,

            ],
            [
                "nome" => "Mirante",
                "bloco_id" => 19,
                "regiao_id" => 142,

            ],
            [
                "nome" => "Eunapolis",
                "bloco_id" => 19,
                "regiao_id" => 143,

            ],
            [
                "nome" => "Núcleo-Alecrim",
                "bloco_id" => 19,
                "regiao_id" => 143,
                
            ],
            [
                "nome" => "TrabEspecial-Barrolandia",
                "bloco_id" => 19,
                "regiao_id" => 143,
                
            ],
            [
                "nome" => "Guarantinga",
                "bloco_id" => 19,
                "regiao_id" => 143,

            ],
            [
                "nome" => "Itabela",
                "bloco_id" => 19,
                "regiao_id" => 143,

            ],
            [
                "nome" => "Núcleo-Itagimirim",
                "bloco_id" => 19,
                "regiao_id" => 143,
                
            ],
            [
                "nome" => "Núcleo-Itapebi",
                "bloco_id" => 19,
                "regiao_id" => 143,
                
            ],
            [
                "nome" => "Juca Rosa",
                "bloco_id" => 19,
                "regiao_id" => 143,

            ],
            [
                "nome" => "Pequi",
                "bloco_id" => 19,
                "regiao_id" => 143,

            ],
            [
                "nome" => "Santa Lucia",
                "bloco_id" => 19,
                "regiao_id" => 143,

            ],
            [
                "nome" => "Itamaraju",
                "bloco_id" => 19,
                "regiao_id" => 144,

            ],
            [
                "nome" => "Caravelas",
                "bloco_id" => 19,
                "regiao_id" => 144,

            ],
            [
                "nome" => "Prado",
                "bloco_id" => 19,
                "regiao_id" => 144,

            ],
            [
                "nome" => "Núcleo-Varzea Alegre",
                "bloco_id" => 19,
                "regiao_id" => 144,
                
            ],
            [
                "nome" => "Porto Seguro",
                "bloco_id" => 19,
                "regiao_id" => 145,

            ],
            [
                "nome" => "Arraial da Ajuda",
                "bloco_id" => 19,
                "regiao_id" => 145,

            ],
            [
                "nome" => "Belmonte",
                "bloco_id" => 19,
                "regiao_id" => 145,

            ],
            [
                "nome" => "Cabralia",
                "bloco_id" => 19,
                "regiao_id" => 145,

            ],
            [
                "nome" => "Trab Especial-Itaporanga",
                "bloco_id" => 19,
                "regiao_id" => 145,
                
            ],
            [
                "nome" => "Trancoso",
                "bloco_id" => 19,
                "regiao_id" => 145,

            ],
            [
                "nome" => "Praça da Bíblia",
                "bloco_id" => 19,
                "regiao_id" => 146,

            ],
            [
                "nome" => "Núcleo-Santo Antonio",
                "bloco_id" => 19,
                "regiao_id" => 146,
                
            ],
            [
                "nome" => "Vilas do Atlântico",
                "bloco_id" => 20,
                "regiao_id" => 147,

            ],
            [
                "nome" => "Portão",
                "bloco_id" => 20,
                "regiao_id" => 147,

            ],
            [
                "nome" => "Itinga",
                "bloco_id" => 20,
                "regiao_id" => 148,

            ],
            [
                "nome" => "Taruma",
                "bloco_id" => 20,
                "regiao_id" => 148,

            ],
            [
                "nome" => "Jaua",
                "bloco_id" => 20,
                "regiao_id" => 149,

            ],
            [
                "nome" => "Arembepe",
                "bloco_id" => 20,
                "regiao_id" => 149,

            ],
            [
                "nome" => "Lauro de Freitas",
                "bloco_id" => 20,
                "regiao_id" => 150,

            ],
            [
                "nome" => "Parque Santa Rita",
                "bloco_id" => 20,
                "regiao_id" => 151,

            ],
            [
                "nome" => "Alto do Itinga",
                "bloco_id" => 20,
                "regiao_id" => 151,

            ],
            [
                "nome" => "Parque São Paulo",
                "bloco_id" => 20,
                "regiao_id" => 151,

            ],
            [
                "nome" => "Vida Nova",
                "bloco_id" => 20,
                "regiao_id" => 152,

            ],
            [
                "nome" => "Caji",
                "bloco_id" => 20,
                "regiao_id" => 152,

            ],
            [
                "nome" => "Vila de Abrante",
                "bloco_id" => 20,
                "regiao_id" => 153,

            ],
            [
                "nome" => "Multirão",
                "bloco_id" => 20,
                "regiao_id" => 153,

            ],
            [
                "nome" => "Vila de Abrante II",
                "bloco_id" => 20,
                "regiao_id" => 153,

            ],
            [
                "nome" => "Catedral",
                "bloco_id" => 21,
                "regiao_id" => 154,

            ],

        ];
        DB::table('igrejas')->insert($igrejas);
    }
}
