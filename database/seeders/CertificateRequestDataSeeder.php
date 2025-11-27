<?php

namespace Database\Seeders;

use App\Models\Certificate\Genre;
use App\Models\Certificate\RentalDeposit;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Kdabrow\SeederOnce\SeederOnce;

class CertificateRequestDataSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Afghanistan', 'iso' => 'AF', 'code' => '93', 'nationality' => 'Afghane'],
            ['name' => 'Afrique du Sud', 'iso' => 'ZA', 'code' => '27', 'nationality' => 'Sud-Africaine'],
            ['name' => 'Albanie', 'iso' => 'AL', 'code' => '355', 'nationality' => 'Albanaise'],
            ['name' => 'Algérie', 'iso' => 'DZ', 'code' => '213', 'nationality' => 'Algérienne'],
            ['name' => 'Allemagne', 'iso' => 'DE', 'code' => '49', 'nationality' => 'Allemande'],
            ['name' => 'Andorre', 'iso' => 'AD', 'code' => '376', 'nationality' => 'Andorrane'],
            ['name' => 'Angola', 'iso' => 'AO', 'code' => '244', 'nationality' => 'Angolaise'],
            ['name' => 'Antigua-et-Barbuda', 'iso' => 'AG', 'code' => '1-268', 'nationality' => 'Antiguaise-et-Barbudienne'],
            ['name' => 'Arabie Saoudite', 'iso' => 'SA', 'code' => '966', 'nationality' => 'Saoudienne'],
            ['name' => 'Argentine', 'iso' => 'AR', 'code' => '54', 'nationality' => 'Argentine'],
            ['name' => 'Arménie', 'iso' => 'AM', 'code' => '374', 'nationality' => 'Arménienne'],
            ['name' => 'Australie', 'iso' => 'AU', 'code' => '61', 'nationality' => 'Australienne'],
            ['name' => 'Autriche', 'iso' => 'AT', 'code' => '43', 'nationality' => 'Autrichienne'],
            ['name' => 'Azerbaïdjan', 'iso' => 'AZ', 'code' => '994', 'nationality' => 'Azerbaïdjanaise'],
            ['name' => 'Bahamas', 'iso' => 'BS', 'code' => '1-242', 'nationality' => 'Bahaméenne'],
            ['name' => 'Bahreïn', 'iso' => 'BH', 'code' => '973', 'nationality' => 'Bahreïnienne'],
            ['name' => 'Bangladesh', 'iso' => 'BD', 'code' => '880', 'nationality' => 'Bangladaise'],
            ['name' => 'Barbade', 'iso' => 'BB', 'code' => '1-246', 'nationality' => 'Barbadienne'],
            ['name' => 'Belgique', 'iso' => 'BE', 'code' => '32', 'nationality' => 'Belge'],
            ['name' => 'Belize', 'iso' => 'BZ', 'code' => '501', 'nationality' => 'Belizienne'],
            ['name' => 'Bénin', 'iso' => 'BJ', 'code' => '229', 'nationality' => 'Béninoise'],
            ['name' => 'Bhoutan', 'iso' => 'BT', 'code' => '975', 'nationality' => 'Bhoutanaise'],
            ['name' => 'Biélorussie', 'iso' => 'BY', 'code' => '375', 'nationality' => 'Biélorusse'],
            ['name' => 'Birmanie', 'iso' => 'MM', 'code' => '95', 'nationality' => 'Birmane'],
            ['name' => 'Bolivie', 'iso' => 'BO', 'code' => '591', 'nationality' => 'Bolivienne'],
            ['name' => 'Bosnie-Herzégovine', 'iso' => 'BA', 'code' => '387', 'nationality' => 'Bosnienne'],
            ['name' => 'Botswana', 'iso' => 'BW', 'code' => '267', 'nationality' => 'Botswanaise'],
            ['name' => 'Brésil', 'iso' => 'BR', 'code' => '55', 'nationality' => 'Brésilienne'],
            ['name' => 'Brunei', 'iso' => 'BN', 'code' => '673', 'nationality' => 'Brunéienne'],
            ['name' => 'Bulgarie', 'iso' => 'BG', 'code' => '359', 'nationality' => 'Bulgare'],
            ['name' => 'Burkina Faso', 'iso' => 'BF', 'code' => '226', 'nationality' => 'Burkinabé'],
            ['name' => 'Burundi', 'iso' => 'BI', 'code' => '257', 'nationality' => 'Burundaise'],
            ['name' => 'Cambodge', 'iso' => 'KH', 'code' => '855', 'nationality' => 'Cambodgienne'],
            ['name' => 'Cameroun', 'iso' => 'CM', 'code' => '237', 'nationality' => 'Camerounaise'],
            ['name' => 'Canada', 'iso' => 'CA', 'code' => '1', 'nationality' => 'Canadienne'],
            ['name' => 'Cap-Vert', 'iso' => 'CV', 'code' => '238', 'nationality' => 'Cap-verdienne'],
            ['name' => 'Chili', 'iso' => 'CL', 'code' => '56', 'nationality' => 'Chilienne'],
            ['name' => 'Chine', 'iso' => 'CN', 'code' => '86', 'nationality' => 'Chinoise'],
            ['name' => 'Chypre', 'iso' => 'CY', 'code' => '357', 'nationality' => 'Chypriote'],
            ['name' => 'Colombie', 'iso' => 'CO', 'code' => '57', 'nationality' => 'Colombienne'],
            ['name' => 'Comores', 'iso' => 'KM', 'code' => '269', 'nationality' => 'Comorienne'],
            ['name' => 'Congo-Brazzaville', 'iso' => 'CG', 'code' => '242', 'nationality' => 'Congolaise'],
            ['name' => 'Congo-Kinshasa', 'iso' => 'CD', 'code' => '243', 'nationality' => 'Congolaise (RDC)'],
            ['name' => 'Corée du Nord', 'iso' => 'KP', 'code' => '850', 'nationality' => 'Nord-coréenne'],
            ['name' => 'Corée du Sud', 'iso' => 'KR', 'code' => '82', 'nationality' => 'Sud-coréenne'],
            ['name' => 'Costa Rica', 'iso' => 'CR', 'code' => '506', 'nationality' => 'Costaricienne'],
            ['name' => 'Côte d\'Ivoire', 'iso' => 'CI', 'code' => '225', 'nationality' => 'Ivoirienne'],
            ['name' => 'Croatie', 'iso' => 'HR', 'code' => '385', 'nationality' => 'Croate'],
            ['name' => 'Cuba', 'iso' => 'CU', 'code' => '53', 'nationality' => 'Cubaine'],
            ['name' => 'Danemark', 'iso' => 'DK', 'code' => '45', 'nationality' => 'Danoise'],
            ['name' => 'Djibouti', 'iso' => 'DJ', 'code' => '253', 'nationality' => 'Djiboutienne'],
            ['name' => 'Dominique', 'iso' => 'DM', 'code' => '1-767', 'nationality' => 'Dominiquaise'],
            ['name' => 'Égypte', 'iso' => 'EG', 'code' => '20', 'nationality' => 'Égyptienne'],
            ['name' => 'Émirats arabes unis', 'iso' => 'AE', 'code' => '971', 'nationality' => 'Émirienne'],
            ['name' => 'Équateur', 'iso' => 'EC', 'code' => '593', 'nationality' => 'Équatorienne'],
            ['name' => 'Érythrée', 'iso' => 'ER', 'code' => '291', 'nationality' => 'Érythréenne'],
            ['name' => 'Espagne', 'iso' => 'ES', 'code' => '34', 'nationality' => 'Espagnole'],
            ['name' => 'Estonie', 'iso' => 'EE', 'code' => '372', 'nationality' => 'Estonienne'],
            ['name' => 'États-Unis', 'iso' => 'US', 'code' => '1', 'nationality' => 'Américaine'],
            ['name' => 'Éthiopie', 'iso' => 'ET', 'code' => '251', 'nationality' => 'Éthiopienne'],
            ['name' => 'Fidji', 'iso' => 'FJ', 'code' => '679', 'nationality' => 'Fidjienne'],
            ['name' => 'Finlande', 'iso' => 'FI', 'code' => '358', 'nationality' => 'Finlandaise'],
            [
                'name' => 'France',
                'iso' => 'FR',
                'code' => '33',
                'nationality' => 'Française',
                'regions' => [
                    [
                        'name' => 'Ile de france',
                        'cities' => [
                            ['name' => 'Paris'],
                            ['name' => 'Colombes'],
                            ['name' => 'Saint Denis'],
                            ['name' => 'Cergy'],
                            ['name' => 'Yerres'],
                            ['name' => 'Melun'],
                            ['name' => 'Puteaux'],
                            ['name' => 'Vincennes'],
                            ['name' => 'Montreuil'],
                            ['name' => 'Clichy'],
                            ['name' => 'Villeparisis'],
                        ],
                    ],
                    [
                        'name' => 'Haut de france',
                        'cities' => [
                            ['name' => 'Lille'],
                            ['name' => 'Loos'],
                            ['name' => 'Roubaix'],
                            ['name' => 'Valenciennes'],
                        ],
                    ],
                    [
                        'name' => 'Rhones Alples',
                        'cities' => [
                            ['name' => 'Villeuerbane'],
                            ['name' => 'Bron'],
                            ['name' => 'Lyon'],
                        ],
                    ],
                    [
                        'name' => 'Bouches-du-Rhones',
                        'cities' => [
                            ['name' => 'Marseille'],
                        ],
                    ],
                    [
                        'name' => 'Nouvelle-Aquitaine',
                        'cities' => [
                            ['name' => 'Bordeaux'],
                        ],
                    ],
                    [
                        'name' => 'Pays de la Loire',
                        'cities' => [
                            ['name' => 'Nantes'],
                            ['name' => 'Angers'],
                        ],
                    ],
                    [
                        'name' => 'Occitanie',
                        'cities' => [
                            ['name' => 'Toulouse'],
                        ],
                    ],
                ],
            ],
            ['name' => 'Gabon', 'iso' => 'GA', 'code' => '241', 'nationality' => 'Gabonaise'],
            ['name' => 'Gambie', 'iso' => 'GM', 'code' => '220', 'nationality' => 'Gambienne'],
            ['name' => 'Géorgie', 'iso' => 'GE', 'code' => '995', 'nationality' => 'Géorgienne'],
            ['name' => 'Ghana', 'iso' => 'GH', 'code' => '233', 'nationality' => 'Ghanéenne'],
            ['name' => 'Grèce', 'iso' => 'GR', 'code' => '30', 'nationality' => 'Grecque'],
            ['name' => 'Grenade', 'iso' => 'GD', 'code' => '1-473', 'nationality' => 'Grenadienne'],
            ['name' => 'Guatemala', 'iso' => 'GT', 'code' => '502', 'nationality' => 'Guatémaltèque'],
            ['name' => 'Guinée', 'iso' => 'GN', 'code' => '224', 'nationality' => 'Guinéenne'],
            ['name' => 'Guinée-Bissau', 'iso' => 'GW', 'code' => '245', 'nationality' => 'Bissau-guinéenne'],
            ['name' => 'Guinée équatoriale', 'iso' => 'GQ', 'code' => '240', 'nationality' => 'Équato-guinéenne'],
            ['name' => 'Guyana', 'iso' => 'GY', 'code' => '592', 'nationality' => 'Guyanienne'],
            ['name' => 'Haïti', 'iso' => 'HT', 'code' => '509', 'nationality' => 'Haïtienne'],
            ['name' => 'Honduras', 'iso' => 'HN', 'code' => '504', 'nationality' => 'Hondurienne'],
            ['name' => 'Hongrie', 'iso' => 'HU', 'code' => '36', 'nationality' => 'Hongroise'],
            ['name' => 'Îles Salomon', 'iso' => 'SB', 'code' => '677', 'nationality' => 'Salomonaise'],
            ['name' => 'Inde', 'iso' => 'IN', 'code' => '91', 'nationality' => 'Indienne'],
            ['name' => 'Indonésie', 'iso' => 'ID', 'code' => '62', 'nationality' => 'Indonésienne'],
            ['name' => 'Irak', 'iso' => 'IQ', 'code' => '964', 'nationality' => 'Irakienne'],
            ['name' => 'Iran', 'iso' => 'IR', 'code' => '98', 'nationality' => 'Iranienne'],
            ['name' => 'Irlande', 'iso' => 'IE', 'code' => '353', 'nationality' => 'Irlandaise'],
            ['name' => 'Islande', 'iso' => 'IS', 'code' => '354', 'nationality' => 'Islandaise'],
            ['name' => 'Israël', 'iso' => 'IL', 'code' => '972', 'nationality' => 'Israélienne'],
            ['name' => 'Italie', 'iso' => 'IT', 'code' => '39', 'nationality' => 'Italienne'],
            ['name' => 'Jamaïque', 'iso' => 'JM', 'code' => '1-876', 'nationality' => 'Jamaïcaine'],
            ['name' => 'Japon', 'iso' => 'JP', 'code' => '81', 'nationality' => 'Japonaise'],
            ['name' => 'Jordanie', 'iso' => 'JO', 'code' => '962', 'nationality' => 'Jordanienne'],
            ['name' => 'Kazakhstan', 'iso' => 'KZ', 'code' => '7', 'nationality' => 'Kazakhstanaise'],
            ['name' => 'Kenya', 'iso' => 'KE', 'code' => '254', 'nationality' => 'Kényane'],
            ['name' => 'Kirghizistan', 'iso' => 'KG', 'code' => '996', 'nationality' => 'Kirghize'],
            ['name' => 'Kiribati', 'iso' => 'KI', 'code' => '686', 'nationality' => 'Kiribatienne'],
            ['name' => 'Koweït', 'iso' => 'KW', 'code' => '965', 'nationality' => 'Koweïtienne'],
            ['name' => 'Laos', 'iso' => 'LA', 'code' => '856', 'nationality' => 'Laotienne'],
            ['name' => 'Lesotho', 'iso' => 'LS', 'code' => '266', 'nationality' => 'Lesothane'],
            ['name' => 'Lettonie', 'iso' => 'LV', 'code' => '371', 'nationality' => 'Lettone'],
            ['name' => 'Liban', 'iso' => 'LB', 'code' => '961', 'nationality' => 'Libanaise'],
            ['name' => 'Libéria', 'iso' => 'LR', 'code' => '231', 'nationality' => 'Libérienne'],
            ['name' => 'Libye', 'iso' => 'LY', 'code' => '218', 'nationality' => 'Libyenne'],
            ['name' => 'Liechtenstein', 'iso' => 'LI', 'code' => '423', 'nationality' => 'Liechtensteinoise'],
            ['name' => 'Lituanie', 'iso' => 'LT', 'code' => '370', 'nationality' => 'Lituanienne'],
            ['name' => 'Luxembourg', 'iso' => 'LU', 'code' => '352', 'nationality' => 'Luxembourgeoise'],
            ['name' => 'Macédoine', 'iso' => 'MK', 'code' => '389', 'nationality' => 'Macédonienne'],
            ['name' => 'Madagascar', 'iso' => 'MG', 'code' => '261', 'nationality' => 'Malgache'],
            ['name' => 'Malaisie', 'iso' => 'MY', 'code' => '60', 'nationality' => 'Malaisienne'],
            ['name' => 'Malawi', 'iso' => 'MW', 'code' => '265', 'nationality' => 'Malawienne'],
            ['name' => 'Maldives', 'iso' => 'MV', 'code' => '960', 'nationality' => 'Maldivienne'],
            ['name' => 'Mali', 'iso' => 'ML', 'code' => '223', 'nationality' => 'Malienne'],
            ['name' => 'Malte', 'iso' => 'MT', 'code' => '356', 'nationality' => 'Maltaise'],
            ['name' => 'Maroc', 'iso' => 'MA', 'code' => '212', 'nationality' => 'Marocaine'],
            ['name' => 'Marshall', 'iso' => 'MH', 'code' => '692', 'nationality' => 'Marshallaise'],
            ['name' => 'Maurice', 'iso' => 'MU', 'code' => '230', 'nationality' => 'Mauricienne'],
            ['name' => 'Mauritanie', 'iso' => 'MR', 'code' => '222', 'nationality' => 'Mauritanienne'],
            ['name' => 'Mexique', 'iso' => 'MX', 'code' => '52', 'nationality' => 'Mexicaine'],
            ['name' => 'Micronésie', 'iso' => 'FM', 'code' => '691', 'nationality' => 'Micronésienne'],
            ['name' => 'Moldavie', 'iso' => 'MD', 'code' => '373', 'nationality' => 'Moldave'],
            ['name' => 'Monaco', 'iso' => 'MC', 'code' => '377', 'nationality' => 'Monégasque'],
            ['name' => 'Mongolie', 'iso' => 'MN', 'code' => '976', 'nationality' => 'Mongole'],
            ['name' => 'Monténégro', 'iso' => 'ME', 'code' => '382', 'nationality' => 'Monténégrine'],
            ['name' => 'Mozambique', 'iso' => 'MZ', 'code' => '258', 'nationality' => 'Mozambicaine'],
            ['name' => 'Namibie', 'iso' => 'NA', 'code' => '264', 'nationality' => 'Namibienne'],
            ['name' => 'Nauru', 'iso' => 'NR', 'code' => '674', 'nationality' => 'Nauruane'],
            ['name' => 'Népal', 'iso' => 'NP', 'code' => '977', 'nationality' => 'Népalaise'],
            ['name' => 'Nicaragua', 'iso' => 'NI', 'code' => '505', 'nationality' => 'Nicaraguayenne'],
            ['name' => 'Niger', 'iso' => 'NE', 'code' => '227', 'nationality' => 'Nigérienne'],
            ['name' => 'Nigeria', 'iso' => 'NG', 'code' => '234', 'nationality' => 'Nigériane'],
            ['name' => 'Norvège', 'iso' => 'NO', 'code' => '47', 'nationality' => 'Norvégienne'],
            ['name' => 'Nouvelle-Zélande', 'iso' => 'NZ', 'code' => '64', 'nationality' => 'Néo-zélandaise'],
            ['name' => 'Oman', 'iso' => 'OM', 'code' => '968', 'nationality' => 'Omanaise'],
            ['name' => 'Ouganda', 'iso' => 'UG', 'code' => '256', 'nationality' => 'Ougandaise'],
            ['name' => 'Ouzbékistan', 'iso' => 'UZ', 'code' => '998', 'nationality' => 'Ouzbèke'],
            ['name' => 'Pakistan', 'iso' => 'PK', 'code' => '92', 'nationality' => 'Pakistanaise'],
            ['name' => 'Palaos', 'iso' => 'PW', 'code' => '680', 'nationality' => 'Palaosienne'],
            ['name' => 'Palestine', 'iso' => 'PS', 'code' => '970', 'nationality' => 'Palestinienne'],
            ['name' => 'Panama', 'iso' => 'PA', 'code' => '507', 'nationality' => 'Panaméenne'],
            ['name' => 'Papouasie-Nouvelle-Guinée', 'iso' => 'PG', 'code' => '675', 'nationality' => 'Papouasienne'],
            ['name' => 'Paraguay', 'iso' => 'PY', 'code' => '595', 'nationality' => 'Paraguayenne'],
            ['name' => 'Pays-Bas', 'iso' => 'NL', 'code' => '31', 'nationality' => 'Néerlandaise'],
            ['name' => 'Pérou', 'iso' => 'PE', 'code' => '51', 'nationality' => 'Péruvienne'],
            ['name' => 'Philippines', 'iso' => 'PH', 'code' => '63', 'nationality' => 'Philippine'],
            ['name' => 'Pologne', 'iso' => 'PL', 'code' => '48', 'nationality' => 'Polonaise'],
            ['name' => 'Portugal', 'iso' => 'PT', 'code' => '351', 'nationality' => 'Portugaise'],
            ['name' => 'Qatar', 'iso' => 'QA', 'code' => '974', 'nationality' => 'Qatarienne'],
            ['name' => 'Roumanie', 'iso' => 'RO', 'code' => '40', 'nationality' => 'Roumaine'],
            ['name' => 'Royaume-Uni', 'iso' => 'GB', 'code' => '44', 'nationality' => 'Britannique'],
            ['name' => 'Russie', 'iso' => 'RU', 'code' => '7', 'nationality' => 'Russe'],
            ['name' => 'Rwanda', 'iso' => 'RW', 'code' => '250', 'nationality' => 'Rwandaise'],
            ['name' => 'Saint-Kitts-et-Nevis', 'iso' => 'KN', 'code' => '1-869', 'nationality' => 'Kittitienne-et-Nevicienne'],
            ['name' => 'Saint-Vincent-et-les-Grenadines', 'iso' => 'VC', 'code' => '1-784', 'nationality' => 'Vincentaise-et-Grenadine'],
            ['name' => 'Sainte-Lucie', 'iso' => 'LC', 'code' => '1-758', 'nationality' => 'Lucienne'],
            ['name' => 'Salvador', 'iso' => 'SV', 'code' => '503', 'nationality' => 'Salvadorienne'],
            ['name' => 'Samoa', 'iso' => 'WS', 'code' => '685', 'nationality' => 'Samoane'],
            ['name' => 'São Tomé-et-Principe', 'iso' => 'ST', 'code' => '239', 'nationality' => 'Santoméenne'],
            ['name' => 'Sénégal', 'iso' => 'SN', 'code' => '221', 'nationality' => 'Sénégalaise'],
            ['name' => 'Serbie', 'iso' => 'RS', 'code' => '381', 'nationality' => 'Serbe'],
            ['name' => 'Seychelles', 'iso' => 'SC', 'code' => '248', 'nationality' => 'Seychelloise'],
            ['name' => 'Sierra Leone', 'iso' => 'SL', 'code' => '232', 'nationality' => 'Sierraléonaise'],
            ['name' => 'Singapour', 'iso' => 'SG', 'code' => '65', 'nationality' => 'Singapourienne'],
            ['name' => 'Slovaquie', 'iso' => 'SK', 'code' => '421', 'nationality' => 'Slovaque'],
            ['name' => 'Slovénie', 'iso' => 'SI', 'code' => '386', 'nationality' => 'Slovène'],
            ['name' => 'Somalie', 'iso' => 'SO', 'code' => '252', 'nationality' => 'Somalienne'],
            ['name' => 'Soudan', 'iso' => 'SD', 'code' => '249', 'nationality' => 'Soudanaise'],
            ['name' => 'Sri Lanka', 'iso' => 'LK', 'code' => '94', 'nationality' => 'Sri-lankaise'],
            ['name' => 'Suède', 'iso' => 'SE', 'code' => '46', 'nationality' => 'Suédoise'],
            ['name' => 'Suisse', 'iso' => 'CH', 'code' => '41', 'nationality' => 'Suisse'],
            ['name' => 'Suriname', 'iso' => 'SR', 'code' => '597', 'nationality' => 'Surinamaise'],
            ['name' => 'Syrie', 'iso' => 'SY', 'code' => '963', 'nationality' => 'Syrienne'],
            ['name' => 'Tadjikistan', 'iso' => 'TJ', 'code' => '992', 'nationality' => 'Tadjike'],
            ['name' => 'Tanzanie', 'iso' => 'TZ', 'code' => '255', 'nationality' => 'Tanzanienne'],
            ['name' => 'Tchad', 'iso' => 'TD', 'code' => '235', 'nationality' => 'Tchadienne'],
            ['name' => 'Tchéquie', 'iso' => 'CZ', 'code' => '420', 'nationality' => 'Tchèque'],
            ['name' => 'Thaïlande', 'iso' => 'TH', 'code' => '66', 'nationality' => 'Thaïlandaise'],
            ['name' => 'Timor oriental', 'iso' => 'TL', 'code' => '670', 'nationality' => 'Est-timoraise'],
            ['name' => 'Togo', 'iso' => 'TG', 'code' => '228', 'nationality' => 'Togolaise'],
            ['name' => 'Tonga', 'iso' => 'TO', 'code' => '676', 'nationality' => 'Tongienne'],
            ['name' => 'Trinité-et-Tobago', 'iso' => 'TT', 'code' => '1-868', 'nationality' => 'Trinidadienne'],
            ['name' => 'Tunisie', 'iso' => 'TN', 'code' => '216', 'nationality' => 'Tunisienne'],
            ['name' => 'Turkménistan', 'iso' => 'TM', 'code' => '993', 'nationality' => 'Turkmène'],
            ['name' => 'Turquie', 'iso' => 'TR', 'code' => '90', 'nationality' => 'Turque'],
            ['name' => 'Tuvalu', 'iso' => 'TV', 'code' => '688', 'nationality' => 'Tuvaluane'],
            ['name' => 'Ukraine', 'iso' => 'UA', 'code' => '380', 'nationality' => 'Ukrainienne'],
            ['name' => 'Uruguay', 'iso' => 'UY', 'code' => '598', 'nationality' => 'Uruguayenne'],
            ['name' => 'Vanuatu', 'iso' => 'VU', 'code' => '678', 'nationality' => 'Vanuatuane'],
            ['name' => 'Vatican', 'iso' => 'VA', 'code' => '379', 'nationality' => 'Vaticane'],
            ['name' => 'Venezuela', 'iso' => 'VE', 'code' => '58', 'nationality' => 'Vénézuélienne'],
            ['name' => 'Vietnam', 'iso' => 'VN', 'code' => '84', 'nationality' => 'Vietnamienne'],
            ['name' => 'Yémen', 'iso' => 'YE', 'code' => '967', 'nationality' => 'Yéménite'],
            ['name' => 'Zambie', 'iso' => 'ZM', 'code' => '260', 'nationality' => 'Zambienne'],
            ['name' => 'Zimbabwe', 'iso' => 'ZW', 'code' => '263', 'nationality' => 'Zimbabwéenne'],
        ];

        foreach ($countries as $countryData) {
            $country = Country::updateOrCreate(
                ['name' => $countryData['name']], [
                    'iso' => $countryData['iso'],
                    'code' => $countryData['code'],
                    'nationality' => $countryData['nationality'],
                    'is_published' => true,
                ]
            );

            if (isset($countryData['regions']) && count($countryData['regions']) > 0) {
                foreach ($countryData['regions'] as $regionData) {
                    $region = $country->regions()->updateOrCreate(
                        ['name' => $regionData['name']],
                        ['is_published' => true]
                    );

                    foreach ($regionData['cities'] as $cityData) {
                        $region->cities()->updateOrCreate(
                            ['name' => $cityData['name']],
                            ['is_published' => true]
                        );
                    }
                }
            }
        }

      /*  $locations = [
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 1,
                'price' => '699',
                'addresses' => '[{"address":"239 bis Rue La Fayette - 75010 Paris"},{"address":"266 Av. Daumesnil - 75012 Paris"},{"address":"6 Rue Marie et Louise - 75010 Paris "}]',
            ],
            [
                'locatable_type' => 'App\\Models\\Region',
                'locatable_id' => 1,
                'price' => '549',
                'addresses' => null,
            ],
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 18,
                'price' => '499',
                'addresses' => null,
            ],
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 12,
                'price' => '450',
                'addresses' => '[{"address":"38, rue du Priez - 59000 Lille"},{"address":"61 Rue Roger Salengro - 59000 Lille"},{"address":"2 Av. Mormal - 59000 Lille"}]',
            ],
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 19,
                'price' => '499',
                'addresses' => '[{"address":"90 Bd Camille Flammarion - 13004 Marseille"},{"address":"39 Rue Capitaine Galinat - 13005 Marseille"},{"address":"26 Rue Jean Martin - 13005 Marseille"}]',
            ],
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 21,
                'price' => '499',
                'addresses' => null,
            ],
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 22,
                'price' => '499',
                'addresses' => null,
            ],
            [
                'locatable_type' => 'App\\Models\\City',
                'locatable_id' => 23,
                'price' => '449',
                'addresses' => null,
            ],
        ];

        foreach ($locations as $location) {
            if ($location['addresses']) {
                $location['addresses'] = json_decode($location['addresses'], true);
            }
            Location::updateOrCreate(
                ['locatable_type' => $location['locatable_type'], 'locatable_id' => $location['locatable_id'], 'is_published' => true],
                $location
            );
        } */

        $genres = [
            ['name' => 'Chambre en colocation', 'is_published' => true],
            ['name' => 'Studio meublé', 'is_published' => true],
        ];

        foreach ($genres as $genre) {
            Genre::updateOrCreate(['name' => $genre['name']], $genre);
        }

        $deposits = [
            ['name' => 'Studely'],
            ['name' => 'Garantme'],
            ['name' => 'Visale'],
            ['name' => 'autres'],
        ];

        foreach ($deposits as $deposit) {
            RentalDeposit::updateOrCreate(['name' => $deposit['name']], ['is_published' => true]);
        }
    }
}
