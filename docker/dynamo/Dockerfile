FROM openjdk:8-jre

RUN cd /usr/lib && \
    curl -L https://s3-us-west-2.amazonaws.com/dynamodb-local/dynamodb_local_latest.tar.gz | tar xz
RUN mkdir -p /var/lib/dynamodb

RUN apt-get update && \
    apt-get install -y supervisor awscli && \
    apt-get clean && \
    rm -fr /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY supervisord.conf /etc/supervisord.conf
RUN mkdir -p /var/log/supervisord

# dynamodb on 8000
EXPOSE 8000

RUN nohup bash -c "java -Djava.library.path=/usr/lib/DynamoDBLocal_lib -jar /usr/lib/DynamoDBLocal.jar -port 8002 -sharedDb  -dbPath /var/lib/dynamodb &" && \
      echo "Dynamo starting, will wait 2s" && \
      sleep 2 && \
      AWS_ACCESS_KEY_ID=key AWS_SECRET_ACCESS_KEY=secret aws --region us-east-1 dynamodb --endpoint-url http://127.0.0.1:8002 create-table --table-name='serverless-payments' --key-schema 'AttributeName=payment_intent,KeyType=HASH' --attribute-definitions 'AttributeName=payment_intent,AttributeType=S' --provisioned-throughput='ReadCapacityUnits=1,WriteCapacityUnits=1'

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
