# 本软件实现vpn服务双因素认证功能,在企业微信里发送验证码三个字会根据不同用户回复不同验证码,用于l2tp vpn双因素登录

#### 1. 首先使用l2tp.sh软件在本服务器上安装好l2tp的vpn服务  
#### 2. 添加防火墙和vpn用户,并测试连接没问题

    *filter表中添加
    -A INPUT -p udp -m state --state NEW -m multiport --dport 4500,500,1701,1194 -j ACCEPT  
    -A OUTPUT -p udp -m multiport --dports 123,53,161 -j ACCEPT  
    *nat表中添加  
    -A POSTROUTING -s 192.168.18.0/24 -o ens161 -j MASQUERADE  

#### 3. 安装nginx+PHP服务  
#### 4. 涉及文件移动,文件读写操作,所以要给logs目录和/etc/ppp/chap-secrets文件给666的读写权限  
#### 5. 在api\v1\verify\index.php和callback\callbackverify.php中配置自己的企业微信$encodingAesKey,$token,$receiveid  