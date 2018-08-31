#!/usr/bin/env bash

curl -XGET "http://localhost:9200"


curl -X POST "localhost:9200/product/_analyze" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'
curl -XGET 'http://localhost:9200/product/_count'
curl -XGET "http://localhost:9200/product/_doc/_search?pretty=true" -H 'Content-Type: application/json' -d '
{
    "query" : {
        "match_all" : {}
    }
}
'

curl -X POST "localhost:9200/product_german/_analyze" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
}
'

curl -X POST "localhost:9200/product_dict_decompounder/_analyze" -H 'Content-Type: application/json' -d'
{
  "field": "short_description",
  "text": "Almgrundstueck an einem Bergsee"
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
curl -X GET "localhost:9200/product_hyphen_decompounder/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 0, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'
curl -X GET "localhost:9200/product_hyphen_decompounder/_search" -H 'Content-Type: application/json' -d'
{
  "from" : 2, "size" : 2,
    "query": {
        "match" : {
            "search" : {
                "query" : "haus alm"
            }
        }
    }
}}'