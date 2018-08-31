#!/usr/bin/env bash
curl -X DELETE "localhost:9200/product"
curl -X PUT "localhost:9200/product" -H 'Content-Type: application/json' -d'
{
    "settings" : {
        "index" : {
            "number_of_shards" : 1,
            "number_of_replicas" : 1
        }
    }
}
'
curl -X PUT "localhost:9200/product/_doc/1" -H 'Content-Type: application/json' -d'
{
    "product_name" : "Passivhaus",
    "short_description" : "Haus mit U-Wert<10kWh/am2",
    "long_description" : "Haus mit U-Wert<10kWh/am2\r\n20m2 Solaranlage\r\n40m2 Photovoltaik\r\n7500l Regenwassertank, ideal an kalten Wintertagen"
}
'
curl -X PUT "localhost:9200/product/_doc/2" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Niedrigenergiehaus",
    "short_description" : "Haus mit U-Wert<45kWh/am2",
    "long_description" : "Haus mit U-Wert<45kWh/am2\r\n20 m2 Solaranlage, ideal an kalten Wintertagen"
}
'
curl -X PUT "localhost:9200/product/_doc/3" -H "Content-Type: application/json" -d '
{
    "product_name" : "Seegrundstück",
    "short_description" : "Seegrundstück am Attersee",
    "long_description" : "Seegrundstück am Attersee mit Seeblick und Bergblick, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product/_doc/4" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Almgrundstück",
    "short_description" : "Almgrundstück an einem Bergsee",
    "long_description" : "Almgrundstück an einem Bergsee mit Zufahrtsstrasse, geschottert und Winterräumung, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product/_doc/5" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Talgrundstück",
    "short_description" : "Grundstück am Talende",
    "long_description" : "Talgrundstück am Ende des Steyerlingtales, wenig Sonne, dafür viel kaltes Bachwasser direkt neben dem Grundstück, ideal für heiße Sommertage"
}
'
curl -X DELETE "localhost:9200/product_german"
curl -X PUT "localhost:9200/product_german" -H 'Content-Type: application/json' -d'
{
   "settings":{
      "index":{
         "number_of_shards":1,
         "number_of_replicas":1
      },
      "analysis":{
         "analyzer":{
            "german":{
               "type":"standard",
               "stopwords": "_none_"
            }
         }
      }
   },
   "mappings":{
      "_doc":{
         "properties":{
            "product_name":{
               "type":"text",
               "analyzer":"german"
            },
            "short_description":{
               "type":"text",
               "analyzer":"german"
            },
            "long_description":{
               "type":"text",
               "analyzer":"german"
            }
         }
      }
   }
}
'
curl -X DELETE "localhost:9200/product_dict_decompounder"
curl -X PUT "localhost:9200/product_dict_decompounder" -H 'Content-Type: application/json' -d'
{
   "settings":{
      "index":{
         "number_of_shards":1,
         "number_of_replicas":1
      },
      "analysis":{
         "filter":{
            "german_decompounder":{
               "type":"hyphenation_decompounder",
               "word_list_path":"analysis/dictionary-de.txt",
               "hyphenation_patterns_path":"analysis/de_DR.xml",
               "only_longest_match":true,
               "min_subword_size":3
            },
            "german_stemmer":{
               "type":"stemmer",
               "language":"light_german"
            }
         },
         "analyzer":{
            "german_decompound":{
               "type":"custom",
               "stopwords": "_none_",
               "tokenizer":"standard",
               "filter":[
                  "lowercase",
                  "german_decompounder",
                  "german_normalization",
                  "german_stemmer"
               ]
            }
         }
      }
   },
   "mappings":{
      "_doc":{
         "properties":{
            "product_name":{
               "type":"text",
               "analyzer":"german_decompound"
            },
            "short_description":{
               "type":"text",
               "analyzer":"german_decompound"
            },
            "long_description":{
               "type":"text",
               "analyzer":"german_decompound"
            }
         }
      }
   }
}
'
curl -X DELETE "localhost:9200/product_hyphen_decompounder"
curl -X PUT "localhost:9200/product_hyphen_decompounder" -H 'Content-Type: application/json' -d'
{
   "settings":{
      "index":{
         "number_of_shards":1,
         "number_of_replicas":1
      },
      "analysis":{
         "filter":{
            "german_decompounder":{
               "type":"hyphenation_decompounder",
               "word_list_path":"analysis/dictionary-de.txt",
               "hyphenation_patterns_path":"analysis/de_DR.xml",
               "only_longest_match":true,
               "min_subword_size":3
            },
            "german_stemmer":{
               "type":"stemmer",
               "language":"light_german"
            }
         },
         "analyzer":{
            "german_decompound":{
               "type":"custom",
               "tokenizer":"standard",
               "filter":[
                  "lowercase",
                  "german_decompounder",
                  "german_normalization",
                  "german_stemmer"
               ]
            }
         }
      }
   },
   "mappings":{
      "_doc":{
         "properties":{
            "product_name":{
               "type":"text",
               "copy_to":"search",
               "analyzer":"german_decompound"
            },
            "short_description":{
               "type":"text",
               "copy_to":"search",
               "analyzer":"german_decompound"
            },
            "long_description":{
               "type":"text",
               "copy_to":"search",
               "analyzer":"german_decompound"
            },
            "search":{
               "type":"text",
               "analyzer":"german_decompound"
            }
         }
      }
   }
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder/_doc/1" -H 'Content-Type: application/json' -d'
{
    "product_name" : "Passivhaus",
    "short_description" : "Haus mit U-Wert<10kWh/am2",
    "long_description" : "Haus mit U-Wert<10kWh/am2\r\n20m2 Solaranlage\r\n40m2 Photovoltaik\r\n7500l Regenwassertank, ideal an kalten Wintertagen"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder/_doc/2" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Niedrigenergiehaus",
    "short_description" : "Haus mit U-Wert<45kWh/am2",
    "long_description" : "Haus mit U-Wert<45kWh/am2\r\n20 m2 Solaranlage, ideal an kalten Wintertagen"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder/_doc/3" -H "Content-Type: application/json" -d '
{
    "product_name" : "Seegrundstück",
    "short_description" : "Seegrundstück am Attersee",
    "long_description" : "Seegrundstück am Attersee mit Seeblick und Bergblick, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder/_doc/4" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Almgrundstück",
    "short_description" : "Almgrundstück an einem Bergsee",
    "long_description" : "Almgrundstück an einem Bergsee mit Zufahrtsstrasse, geschottert und Winterräumung, ideal für heiße Sommertage"
}
'
curl -X PUT "localhost:9200/product_hyphen_decompounder/_doc/5" -H 'Content-Type: application/json' -d '
{
    "product_name" : "Talgrundstück",
    "short_description" : "Grundstück am Talende",
    "long_description" : "Talgrundstück am Ende des Steyerlingtales, wenig Sonne, dafuer viel kaltes Bachwasser direkt neben dem Grundstück, ideal für heiße Sommertage"
}
'